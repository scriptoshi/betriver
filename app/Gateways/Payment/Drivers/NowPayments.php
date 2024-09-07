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
use Exception;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Log;

class NowPayments implements Provider
{

    public ?string $jwt;
    public ?Carbon $expires;
    public $name = 'nowpayments';
    public static string $url = 'https://api.nowpayments.io/v1';
    public static string $sandbox = 'https://api-sandbox.nowpayments.io/v1';
    public function __construct(
        public ?string $api_key = null,
        public ?string $api_secret = null,
    ) {}

    /**
     * Get the name of the gateway
     */
    public function getName(): string
    {
        return $this->name;
    }

    public static function endpoint($link)
    {
        $api = config('app.sandbox') ? static::$sandbox : static::$url;
        return "$api/$link";
    }

    /**
     * @inherit doc
     */
    public function getConfig(): Collection
    {
        $config = settings()->for('nowpayments');
        if ($config->count() == 0) {
            settings()->set('nowpayments.name', 'Nowpayments');
            settings()->set('nowpayments.logo', "https://cdn.theorg.com/db1c9d51-e2d5-4ddc-9e71-2719c0106e92_medium.jpg");
            settings()->set('nowpayments.enable_withdraw', 'true');
            settings()->set('nowpayments.auto_approve_withdraw_address', 'true');
            settings()->set('nowpayments.enable_deposit', 'true');
            settings()->set('nowpayments.api_key', null);
            settings()->set('nowpayments.api_secret', null);
            settings()->set('nowpayments.max_withdraw_limit', 0);
            settings()->set('nowpayments.min_withdraw_limit', 20);
            $config = settings()->for('nowpayments');
        }
        return $config;
    }
    /**
     * @inherit doc
     */
    public function setConfig(Request $request): Collection
    {
        settings()->set('nowpayments.name', $request->name);
        settings()->set('nowpayments.logo', $request->logo);
        settings()->set('nowpayments.enable_withdraw', $request->boolean('enable_withdraw') ? 'true' : 'false');
        settings()->set('nowpayments.auto_approve_withdraw_address', $request->boolean('auto_approve_withdraw_address') ? 'true' : 'false');
        settings()->set('nowpayments.enable_deposit', $request->boolean('enable_deposit') ? 'true' : 'false');
        settings()->set('nowpayments.api_key', $request->api_key);
        settings()->set('nowpayments.api_secret', $request->api_secret);
        settings()->set('nowpayments.max_withdraw_limit', $request->max_withdraw_limit);
        settings()->set('nowpayments.min_withdraw_limit', $request->min_withdraw_limit);
        return settings()->for('nowpayments');
    }


    /**
     * update currencies supported by gateway
     */
    public function updateCurrencies()
    {
        if (!$this->api_key) return;
        $currencies = Cache::remember('now-payments-all-currencies-full', 60 * 60 * 24, function () {
            $currencies = Curl::to(static::endpoint('full-currencies'))
                ->withHeader("x-api-key: {$this->api_key}")
                ->asJson()
                ->get();
            return  $currencies->currencies;
        });
        $accepted = Cache::remember(
            'now-payments-accepted-currencies-full',
            60 * 60,
            function () {
                return Curl::to(static::endpoint('merchant/coins'))
                    ->withHeader("x-api-key: {$this->api_key}")
                    ->asJson()
                    ->get();
            }
        );
        $coingeckolist = collect(json_decode(File::get(storage_path('app/coingeckids.json'))));
        $rates = Rate::symbols();
        collect($currencies)->filter(function ($cur) use ($accepted) {
            return in_array($cur->code, $accepted->selectedCurrencies) &&  $cur->enable;
        })->each(function ($currency) use ($coingeckolist,  $rates) {
            $symbol = $coingeckolist->get($currency->cg_id)?->symbol ?? null;
            if (!$symbol) return;
            $rate = $rates[strtolower($symbol)] ??  $rates[strtoupper($symbol)] ?? null;
            if (!$rate) return;
            Currency::query()->updateOrCreate([
                "code" => $currency->code,
                'gateway' => $this->name,
            ], [
                "symbol" =>  strtoupper($symbol),
                "name" => $currency->name,
                "regex" => $currency->wallet_regex ?? null,
                "logo_url" => $currency->logo_url ? 'https://nowpayments.io' . str($currency->logo_url)->replace('image/', 'images/') : null,
                "network" => $currency->network ?? null,
                "contract" => $currency->contract ?? null,
                "explorer" => $currency->explorer_link_hash ?? null,
                "rate" => $rate,
                "precision" => $currency->precision
            ]);
        });
    }



    /**
     * @inherit doc
     */
    public  function deposit(Deposit $deposit)
    {
        $payment = Curl::to(static::endpoint('payment'))
            ->withData([
                'order_id' => $deposit->uuid,
                'ipn_callback_url' =>  route('deposits.webhooks', ['provider' => $this->name, 'deposit' => $deposit->id]),
                'pay_currency' => $deposit->gateway_currency,
                'price_amount' => $deposit->gross_amount,
                'price_currency' => $deposit->amount_currency,
            ])
            ->withHeader("x-api-key: {$this->api_key}")
            ->asJson()
            ->returnResponseObject()
            ->post();
        if (isset($payment->error)) {
            $deposit->gateway_error = __('Failed to inialize transaction api. :error', ['error' => $payment->error]);
            $deposit->status = DepositStatus::FAILED;
            $deposit->save();
            return back()->with('error', __('Failed to inialize transaction api. :error', ['error' => $payment->error]));
        }
        if ($payment->status > 299) {
            $deposit->gateway_error = __('Trasanction Request Rejected. Invalid API Creds ?');
            $deposit->status = DepositStatus::FAILED;
            $deposit->save();
            return back()->with('error', __('Trasanction Request Rejected. Invalid API Creds ?'));
        }
        $deposit->data = $payment->content;
        $deposit->gateway_amount = $payment->content->pay_amount;
        $deposit->deposit_address = $payment->content->pay_address;
        $deposit->remoteId = $payment->content->payment_id;
        $deposit->status = DepositStatus::PROCESSING;
        $deposit->save();
        return redirect()->route('deposits.show', $deposit->uuid);
    }

    public function returned(Request $request, Deposit $deposit)
    {
        return redirect()->route('deposits.show', $deposit->uuid);
    }

    /**
     * @inherit doc
     */
    public function webhook(Request $request, string $type = 'deposit')
    {
        if ($type = 'withdraw') {
            if ($this->verifyIPN($request)) {
                $withdraw = Withdraw::query()->where('remoteId', $request->id)->first();
                if (!$withdraw) return;
                if ($request->status === 'finished') {
                    $withdraw->status = WithdrawStatus::COMPLETE;
                }
                if ($request->status === 'processing') {
                    $withdraw->status = WithdrawStatus::PROCESSING;
                }
                if ($request->status === 'rejected') {
                    $withdraw->status = WithdrawStatus::REJECTED;
                }
                if ($request->status === 'failed') {
                    $withdraw->status = WithdrawStatus::FAILED;
                }
                $withdraw->save();
            }
            return;
        }

        if (
            !$this->verifyIPN($request) ||
            !in_array($request->payment_status, ['confirmed', 'finished']) ||
            $request->actually_paid < $request->pay_amount
        ) return;

        if (
            $deposit = Deposit::query()
            ->where('status', DepositStatus::PROCESSING)
            ->where('uuid', $request->order_id)->first()
        ) {
            $deposit->status = DepositStatus::COMPLETE;
            $deposit->save();
            if ($deposit->auto_approve) {
                app(DepositTx::class)->create($deposit);
            }
        }
    }

    public  function withdraw(Collection $withdraws)
    {
        if (now()->gt($this->expires)) throw new Exception('Login Expired');
        $response = Curl::to(static::endpoint('payout'))
            ->withData([
                "ipn_callback_url" => route('webhooks.withdraw', ['provider' => $this->name]),
                "withdrawals" => $withdraws->map(function (Withdraw $withdraw) {
                    return [
                        "address" => $withdraw->to,
                        "currency" => $withdraw->gateway_currency,
                        "fiat_currency" => $withdraw->amount_currency,
                        "fiat_amount" => $withdraw->amount,
                        "unique_external_id" => $withdraw->uuid,
                    ];
                })->all(),
            ])
            ->withAuthorization($this->jwt)
            ->withHeader("x-api-key: {$this->api_key}")
            ->post();
        if (!$response->id) return back()->withErrors('Withdraw Failed. Check API Credentails');
        foreach ($response->withdrawals as $withdrawal) {
            Withdraw::query()
                ->where('uuid', $withdrawal->unique_external_id)
                ->update([
                    'gateway_amount' => $withdrawal->amount,
                    'batchId' => $response->id,
                    'remoteId' => $withdrawal->id,
                    'data' =>  $withdrawal,
                    'status' => WithdrawStatus::PROCESSING
                ]);
        }
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
        $response = Curl::to(static::endpoint("payout/{$withdraw->remoteId}"))
            ->withHeader("x-api-key: {$this->api_key}")
            ->get();
        $status = $response->status ?? $response[0]->status;
        if (!$status) {
            Log::error("Failed to get withdrawal status from Nowpayments for withdraw {$withdraw->id}");
            return false;
        }
        $withdraw->status = match ($response->status ?? $response[0]->status) {
            'finished' => WithdrawStatus::COMPLETE,
            'creating', 'sending', 'processing' => WithdrawStatus::PROCESSING,
            'rejected' => WithdrawStatus::REJECTED,
            'failed' => WithdrawStatus::FAILED,
            default => WithdrawStatus::PROCESSING,
        };
        $withdraw->save();
        return true;
    }


    public  function updatePayoutStatus(Collection $withdraws)
    {
        foreach ($withdraws as  $withdraw) {
            $this->updateWithdrawStatus($withdraw);
        }
    }
    /**
     * Get JWT token for payout requests
     * Valid for 5 minutes;
     */
    public function login($email, $password)
    {
        $jwt = Curl::to(static::endpoint('auth'))
            ->withData([
                'email' => $email,
                'password' => $password
            ])
            ->post();
        $this->jwt = $jwt->token;
        $this->expires = now()->addMinutes(5);
    }

    /**
     * Verify mass payout request
     */
    public function verifyPayout(string $payoutId, $verification_code)
    {
        if (now()->gt($this->expires)) throw new Exception('Login Expired');
        $response = Curl::to(static::endpoint("payout/$payoutId/verify"))
            ->withData(["verification_code" => $verification_code])
            ->withAuthorization($this->jwt)
            ->withHeader("x-api-key: {$this->api_key}")
            ->post();
        return back()->with('message', $response->message);
    }

    /**
     * Checks IPN received from Nowpayments IPN
     * @return bool
     */
    protected function verifyIPN(Request $request): bool
    {
        $receivedHmac = $request->header('x-nowpayments-sig');
        if ($receivedHmac) {
            $requestData = collect($request->all())->sortKeys(); // Utilize Collection for sorting
            $sortedRequestJson = json_encode($requestData, JSON_UNESCAPED_SLASHES);
            if ($requestData->isNotEmpty()) {
                $hmac = hash_hmac("sha512", $sortedRequestJson, trim($this->api_secret));
                return $hmac === $receivedHmac;
            }
        }
        return false;
    }
}
