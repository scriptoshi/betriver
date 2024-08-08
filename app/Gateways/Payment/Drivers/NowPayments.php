<?php

namespace App\Gateways\Payment\Drivers;

use App\Enums\DepositStatus;
use App\Enums\WithdrawStatus;
use App\Gateways\Payment\Actions\DepositTx;
use App\Gateways\Payment\Contracts\Provider;
use App\Models\Deposit;
use App\Models\Withdraw;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Support\Carbon;

class NowPayments implements Provider
{

    public ?string $jwt;
    public ?Carbon $expires;
    public $name = 'nowpayments';
    public string $endpoint = 'https://api.nowpayments.io/v1/payment';
    public function __construct(
        public ?string $api_key = null,
        public ?string $api_secret = null,
    ) {
    }

    /**
     * Get the name of the gateway
     */
    public function getName(): string
    {
        return $this->name;
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
            settings()->set('nowpayments.enable_deposit', 'true');
            settings()->set('nowpayments.currencies', 'BTC,USDT,USDC');
            settings()->set('nowpayments.api_key', null);
            settings()->set('nowpayments.api_secret', null);
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
        settings()->set('nowpayments.enable_deposit', $request->boolean('enable_deposit') ? 'true' : 'false');
        settings()->set('nowpayments.currencies', $request->currencies);
        settings()->set('nowpayments.api_key', $request->api_key);
        settings()->set('nowpayments.api_secret', $request->api_secret);
        return settings()->for('nowpayments');
    }



    /**
     * @inherit doc
     */
    public  function deposit(Deposit $deposit)
    {
        $payment = Curl::to($this->endpoint)
            ->withData([
                'order_id' => $deposit->uuid,
                'ipn_callback_url' =>  route('webhooks.deposit', ['provider' => $this->name]),
                'pay_currency' => $deposit->gateway_currency,
                'price_amount' => $deposit->gross_amount,
                'price_currency' => $deposit->amount_currency,
            ])
            ->withHeader("x-api-key: {$this->api_key}")
            ->asJson()
            ->returnResponseObject()
            ->post();
        if (isset($payment->error)) {
            return back()->with('error', __('Failed to inialize transaction api. :error', ['error' => $payment->error]));
        }
        if ($payment->status > 299) {
            return back()->with('error', __('Trasanction Request Rejected. Invalid API Creds ?'));
        }
        $deposit->data = $payment->content;
        $deposit->gateway_amount = $payment->content->pay_amount;
        $deposit->deposit_address = $payment->content->pay_address;
        $deposit->remoteId = $payment->content->payment_id;
        $deposit->status = DepositStatus::PROCESSING;
        $deposit->save();
        return redirect()->route('deposits.show', $deposit);
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
        $response = Curl::to("https://api.nowpayments.io/v1/payout")
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
                    'batch_id' => $response->id,
                    'remoteId' => $withdrawal->id,
                    'data' =>  $withdrawal
                ]);
        }
    }

    public  function updatePayoutStatus(Collection $withdraws)
    {
        foreach ($withdraws as  $withdraw) {
            $response = Curl::to("https://api.nowpayments.io/v1/payout/{$withdraw->remoteId}")
                ->withHeader("x-api-key: {$this->api_key}")
                ->get();
            if ($response->status === 'finished') {
                $withdraw->status = WithdrawStatus::COMPLETE;
            }
            if ($response->status === 'processing') {
                $withdraw->status = WithdrawStatus::PROCESSING;
            }
            if ($response->status === 'rejected') {
                $withdraw->status = WithdrawStatus::REJECTED;
            }
            if ($response->status === 'failed') {
                $withdraw->status = WithdrawStatus::FAILED;
            }
            $withdraw->save();
        }
    }
    /**
     * Get JWT token for payout requests
     * Valid for 5 minutes;
     */
    public function login($email, $password)
    {
        $jwt = Curl::to('https://api.nowpayments.io/v1/auth')
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
        $response = Curl::to("https://api.nowpayments.io/v1/payout/$payoutId/verify")
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
