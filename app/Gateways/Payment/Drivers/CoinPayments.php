<?php

namespace App\Gateways\Payment\Drivers;

use App\Enums\DepositStatus;
use App\Enums\WithdrawStatus;
use App\Gateways\Payment\Actions\DepositTx;
use App\Gateways\Payment\Contracts\Provider;
use App\Models\Currency;
use App\Models\Deposit;
use App\Models\Withdraw;
use App\Support\Rate;
use Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Ixudra\Curl\Facades\Curl;
use Log;
use Str;

class CoinPayments implements Provider
{

    public static $endpoint = 'https://www.coinpayments.net/api.php';
    public static $sandbox = 'https://www.coinpayments.net/api.php';
    public $name = 'coinpayments';
    public function __construct(
        public ?string $public_key = null,
        public ?string $private_key = null,
        public ?string $merchant_id = null,
        public ?string $ipn_secret = null
    ) {}

    public function getName()
    {
        return $this->name;
    }

    public function getConfig(): Collection
    {
        $config = settings()->for('coinpayments');
        if ($config->count() == 0) {
            settings()->set('coinpayments.name', 'Coin Payments');
            settings()->set('coinpayments.logo', "https://avatars.githubusercontent.com/u/6100871");
            settings()->set('coinpayments.enable_withdraw', 'true');
            settings()->set('coinpayments.auto_approve_withdraw_address', 'true');
            settings()->set('coinpayments.enable_deposit', 'true');
            settings()->set('coinpayments.public_key', null);
            settings()->set('coinpayments.private_key', null);
            settings()->set('coinpayments.merchant_id', null);
            settings()->set('coinpayments.ipn_secret', Str::random(30));
            settings()->set('coinpayments.max_withdraw_limit', 0);
            settings()->set('coinpayments.min_withdraw_limit', 20);
            $config = settings()->for('coinpayments');
        }
        return $config;
    }

    public function setConfig(Request $request): Collection
    {
        settings()->set('coinpayments.name', $request->name);
        settings()->set('coinpayments.logo', $request->logo);
        settings()->set('coinpayments.enable_withdraw', $request->boolean('enable_withdraw') ? 'true' : 'false');
        settings()->set('coinpayments.auto_approve_withdraw_address', $request->boolean('auto_approve_withdraw_address') ? 'true' : 'false');
        settings()->set('coinpayments.enable_deposit', $request->boolean('enable_deposit') ? 'true' : 'false');
        settings()->set('coinpayments.public_key', $request->public_key);
        settings()->set('coinpayments.private_key', $request->private_key);
        settings()->set('coinpayments.merchant_id', $request->merchant_id);
        settings()->set('coinpayments.ipn_secret', $request->ipn_secret);
        settings()->set('coinpayments.max_withdraw_limit', $request->max_withdraw_limit);
        settings()->set('coinpayments.min_withdraw_limit', $request->min_withdraw_limit);
        return settings()->for('coinpayments');
    }

    /**
     * Get all the currencies supported by the gateway
     */
    public function updateCurrencies()
    {
        if (!$this->public_key) return;
        $accepted = Cache::remember('coinpayment-accepted-currencies', 60 * 60, function () {
            return  $this->coinpayment('rates', ['accepted' => 1]);
        });
        collect($accepted->result)->each(function ($currency, $symbol) {
            $rate = Rate::btcToUsd($currency->rate_btc, 16);
            if (floatval($rate) == 0) return;
            Currency::query()->updateOrCreate([
                "code" => $symbol,
                'gateway' => $this->name,
            ], [
                "symbol" => str($symbol)->before('.'),
                "name" => $currency->name,
                "regex" => null,
                "logo_url" =>  $currency->image ?? null,
                "chain" => $currency->chain ?? null,
                'contract' => $currency->contract ?? null,
                'explorer' => $currency->explorer ?? null,
                'rate' => Rate::btcToUsd($currency->rate_btc, 16),
                'precision' => 16
            ]);
        });
    }


    /**
     * Handle deposit request.
     *
     * @param  Deposit $deposit
     */
    public function deposit(Deposit $deposit)
    {

        $response = $this->coinpayment('create_transaction', [
            'custom' => $deposit->uuid,
            'buyer_email' => $deposit->user->email,
            'amount' => $deposit->gross_amount, // usd
            'currency1' => $deposit->amount_currency, // usd
            'currency2' => $deposit->gateway_currency, // btc
            'ipn_url' => route('deposits.webhooks', ['provider' => $this->name, 'deposit' => $deposit->id]),
        ]);
        if ($response->error != 'ok')
            return back()->with('error', __('Failed to inialize transaction api. :error', ['error' => $response->error]));
        $deposit->data = $response->result;
        $deposit->remoteId = $response->result->txn_id;
        $deposit->deposit_address = $response->result->address;
        $deposit->gateway_amount = $response->result->amount;
        $deposit->status = DepositStatus::PROCESSING;
        $deposit->save();
        return redirect()->route('deposits.show', $deposit);
    }

    /**
     * Handle IPN Webhook from provider.
     *
     * @param  Request $request
     */
    public function webhook(Request $request, string $type = 'deposit')
    {
        if (!$this->verifyIpn($request)) return;
        if ($request->status < 100 && $request->status != 2) return;
        if (
            $deposit = Deposit::query()
            ->where('status', DepositStatus::PROCESSING)
            ->where('uuid', $request->custom)->first()
        ) {
            $deposit->status = DepositStatus::COMPLETE;
            $deposit->save();
            if ($deposit->auto_approve) {
                app(DepositTx::class)->create($deposit);
            }
        }
    }

    /**
     * Batch withdraw via coinpayments
     * Requires confirmation email from coinpayments
     *
     * @param  Withdraw $withdraw
     */
    public function withdraw(Collection $withdraws)
    {
        $response = $this->coinpayment(
            'create_mass_withdrawal',
            $withdraws->flatMap(function (Withdraw $withdraw) {
                return [
                    "wd[{$withdraw->id}][amount]" =>  $withdraw->amount, //usd
                    "wd[{$withdraw->id}][currency2]" => $withdraw->amount_currency, //usd
                    "wd[{$withdraw->id}][currency]" => $withdraw->gateway_currency,
                    "wd[{$withdraw->id}][address]" => $withdraw->to,
                    "wd[{$withdraw->id}][ipn_url]" =>  route('webhook.withdraw', ['provider' => $this->name]),
                ];
            })
        );
        if ($response->error != 'ok') return back()->with('error', __("Withdraw Failed: " . $response->error));
        $hasError = false;
        $batchId = Str::random(32);
        foreach ($response->result as $id => $res) {
            if ($res->error != 'ok') {
                Withdraw::where('id', $id)->update([
                    'status' => WithdrawStatus::FAILED,
                    'gateway_error' => $res->error
                ]);
                $hasError = true;
                continue;
            }
            Withdraw::where('id', $id)->update([
                'status' => WithdrawStatus::PROCESSING,
                'remoteId' => $res->id,
                'batchId' => $batchId,
                'gateway_amount' => $res->amount
            ]);
        }
        if ($hasError) return back()->with('error', __("Some withdraws failed to process."));
        return back()->with('success', __("All withdraws processed successfully"));
    }


    /**
     * Update the status of a withdraw (payout) at CoinPayments.
     *
     * @param string $withdrawId The ID of the withdraw in our system
     * @return bool Returns true if the update was successful, false otherwise
     */
    public function updateWithdrawStatus(Withdraw $withdraw): bool
    {

        if (!$withdraw->remoteId) {
            Log::error("Withdraw {$withdraw->id} does not have a remote ID");
            return false;
        }

        $response = $this->coinpayment('get_withdrawal_info', [
            'id' => $withdraw->remoteId,
        ]);

        if (!$response || !isset($response->result)) {
            Log::error("Failed to get withdrawal info from CoinPayments for withdraw {$withdraw->id}");
            return false;
        }

        $result = $response->result;

        $newStatus = $this->mapCoinPaymentsStatus($result->status);

        if ($newStatus !== $withdraw->status) {
            $withdraw->status = $newStatus;
            $withdraw->data = array_merge($withdraw->data ?? [], ['coinpayments_status' => $result]);
            $withdraw->save();

            // You might want to trigger additional actions here based on the new status
            // For example, if the status is now COMPLETE, you might want to credit the user's account
        }

        return true;
    }

    /**
     * Map CoinPayments status to our WithdrawStatus enum.
     *
     * @param int $coinPaymentsStatus
     * @return WithdrawStatus
     */
    private function mapCoinPaymentsStatus(int $coinPaymentsStatus): WithdrawStatus
    {
        return match ($coinPaymentsStatus) {
            0 => WithdrawStatus::PENDING,
            1 => WithdrawStatus::PROCESSING,
            2 => WithdrawStatus::COMPLETE,
            -1 => WithdrawStatus::CANCELLED,
            default => WithdrawStatus::FAILED,
        };
    }


    /**
     * Make an API call to CoinPayments.
     *
     * @param string $command
     * @param array $data
     * @return object|null
     */

    protected function coinpayment($cmd, $data = [])
    {
        $request = [...$data, 'version' => 1, 'cmd' => $cmd, 'key' => $this->public_key, 'format' => 'json'];
        // Calculate the HMAC signature on the POST data
        $hmac = hash_hmac('sha512', http_build_query($request, '', '&'), $this->private_key);
        return Curl::to(static::$endpoint)
            ->withData($request)
            ->withHeader('HMAC: ' . $hmac)
            ->withContentType('application/x-www-form-urlencoded')
            ->asJsonResponse()
            ->post();
    }

    protected function verifyIpn(Request $request)
    {
        if (!empty($request->merchant) && $request->merchant != trim($this->merchant_id)) {
            return false;
        }
        $content = $request->getContent();
        $hmac = hash_hmac("sha512", $content, trim($this->ipn_secret));
        return hash_equals($hmac, $request->server('HTTP_HMAC'));
    }

    public function returned(Request $request, Deposit $deposit)
    {
        return redirect()->route('deposits.show', $deposit->uuid);
    }
}
