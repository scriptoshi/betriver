<?php

namespace App\Gateways\Payment\Drivers;

use App\Enums\DepositStatus;
use App\Enums\WithdrawStatus;
use App\Gateways\Payment\Contracts\Provider;
use Inertia\Inertia;
use App\Gateways\Payment\Actions\DepositTx;
use App\Models\Deposit;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Ixudra\Curl\Facades\Curl;

class Payeer implements Provider
{
    public static $baseUrl = 'https://payeer.com/ajax/api/api.php';
    public $name = 'payeer';
    public function __construct(
        public ?string $shop = null,
        public ?string $merchant_key = null,
        public ?string $accountNumber = null,
        public ?string $apiId = null,
        public ?string $apiSecret = null
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
     * Get the configuration for the gateway
     */

    public function getConfig(): Collection
    {
        $config = settings()->for('payeer');
        if ($config->count() == 0) {
            settings()->set('payeer.name', 'Payeer');
            settings()->set('payeer.logo', "https://payeer.com/static/icons/apple-touch-icon.png");
            settings()->set('payeer.enable_withdraw', 'true');
            settings()->set('payeer.enable_deposit', 'true');
            settings()->set('payeer.currencies', 'USD,EUR');
            settings()->set('payeer.shop', null);
            settings()->set('payeer.merchant_key', null);
            settings()->set('payeer.accountNumber', null);
            settings()->set('payeer.apiId', null);
            settings()->set('payeer.apiSecret', null);
            $config = settings()->for('payeer');
        }
        return $config;
    }

    /**
     * sve the configuration for the gateway
     */
    public function setConfig(Request $request): Collection
    {
        settings()->set('payeer.name', $request->name);
        settings()->set('payeer.logo', $request->logo);
        settings()->set('payeer.enable_withdraw', $request->boolean('enable_withdraw') ? 'true' : 'false');
        settings()->set('payeer.enable_deposit', $request->boolean('enable_deposit') ? 'true' : 'false');
        settings()->set('payeer.currencies', $request->currencies);
        settings()->set('payeer.shop', $request->shop);
        settings()->set('payeer.merchant_key', $request->merchant_key);
        settings()->set('payeer.accountNumber', $request->accountNumber);
        settings()->set('payeer.apiId', $request->apiId);
        settings()->set('payeer.apiSecret', $request->apiSecret);
        return settings()->for('payeer');
    }

    /**
     * Handle deposit request.
     *
     * @param  Deposit $deposit
     */
    public function deposit(Deposit $deposit)
    {
        $m_amount = number_format($deposit->gross_amount, 2, '.', '');
        $m_shop = $this->shop;
        $m_order_id = $deposit->uuid;
        $m_curr = $deposit->amount_currency;
        $m_desc = base64_encode('Topup Account balance');
        $urlParams = [
            'm_shop' => $m_shop,
            'm_orderid' => $m_order_id,
            'm_amount' => $m_amount,
            'm_curr' => $m_curr,
            'm_desc' => $m_desc,
            'm_sign' => strtoupper(hash('sha256', implode(":", [$m_shop, $m_order_id, $m_amount, $m_curr, $m_desc, $this->merchant_key])))
        ];
        $url = 'https://payeer.com/merchant/?' . http_build_query($urlParams);
        return Inertia::location($url);
    }

    /**
     * Handle IPN Webhook from provider.
     *
     * @param  Request $request
     */
    public function webhook(Request $request, string $type = 'deposit')
    {
        if (!$this->verifyIpn($request)) return;
        $deposit = Deposit::where('uuid', $request->m_orderid)->first();
        if (
            $request->m_amount < floatval(round($amount ?? 0, 2)) ||
            $request->m_curr != $deposit->fiat_currency ||
            $request->m_status != 'success'  ||
            $deposit->status != DepositStatus::PROCESSING
        ) return;
        $deposit->status = DepositStatus::COMPLETE;
        $deposit->save();
        if ($deposit->auto_approve) {
            app(DepositTx::class)->create($deposit);
        }
    }



    /**
     * send funds to payeer user.
     * Payeer withdrawals only support USD for now
     * @param  Withdraw $withdraw
     */
    public function withdraw(Collection $withdraws)
    {

        foreach ($withdraws as $withdraw) {
            if ($withdraw->status != WithdrawStatus::APPROVED) continue;
            $response = $this->call('transfer', [
                'curIn' =>  $withdraw->fiat_currency,
                'sum' => $withdraw->amount,
                'curOut' => $withdraw->fiat_currency,
                'to' => $withdraw->to
            ]);
            if ($response->success) {
                $withdraw->remoteId = $response->historyId;
                $withdraw->status = WithdrawStatus::COMPLETE;
                $withdraw->save();
            }
            if (!$response->errors) continue;

            $withdraw->gateway_error = match ($response->errors[0]) {
                'balanceError' => 'insufficient funds for transfer',
                'balanceError000' => 'insufficient funds for transfer given limitations on the account',
                'transferHimselfForbidden' => 'you cannot transfer funds to yourself (when the withdrawal currency and deposit currency are the same)',
                'transferError' => 'transfer error, try again later',
                'convertForbidden' => 'automatic exchange of curIn for curOut is temporarily prohibited',
                'protectDay_1_30' => 'error entering transfer protection period',
                'protectCodeNotEmpty' => 'the protection code cannot be blank when protection is active',
                'sumNotNull' => 'the amounts sent and received cannot be zero',
                'sumInNotMinus' => 'the amount sent cannot be negative',
                'sumOutNotMinus' => 'the amount received cannot be negative',
                'fromError' => 'sender not found',
                'toError' => 'recipient entered incorrectly',
                'curInError' => 'withdrawal currency not supported',
                'curOutError' => 'reception currency not supported',
                'outputHold' => 'Issues have arisen regarding your activity. Please contact Technical Support',
                'transferToForbiddenCountry' => 'Transfer to customers in certain countries is prohibited',
            };
            $withdraw->status = WithdrawStatus::FAILED;
            $withdraw->save();
        };
    }




    /**
     * 
     */
    public function verifyIpn(Request $request)
    {
        if (!isset($request->m_operation_id) || !isset($request->m_sign))
            return false;
        $m_sign = strtoupper(hash('sha256', implode(":", [
            $request->m_operation_id,
            $request->m_operation_ps,
            $request->m_operation_date,
            $request->m_operation_pay_date,
            $request->m_shop,
            $request->m_orderid,
            $request->m_amount,
            $request->m_curr,
            $request->m_desc,
            $request->m_status,
            $this->merchant_key,
        ])));
        return $request->m_sign == $m_sign;
    }


    /**
     * Check if user account exists
     *
     * @param $user
     * @return boolean
     */
    public function checkUser($user)
    {
        $response = $this->call('checkUser', ['user' => $user]);
        return (bool) $response->success;
    }

    /**
     * make an api call to payeer
     * @param $action
     * @param array $params
     * @return object
     * @throws ValidationException
     */
    private function call($action, $params = []): object
    {
        $response = Curl::to(static::$baseUrl)
            ->withData([
                ...$params,
                'account' => $this->accountNumber,
                'apiId' => $this->apiId,
                'apiPass' => $this->apiSecret,
                'action' => $action
            ])
            ->withContentType('application/x-www-form-urlencoded')
            ->asJsonResponse()
            ->returnResponseObject()
            ->post();
        if (isset($response->error)) {
            throw ValidationException::withMessages(['withdrawal' => [$response->error]]);
        }
        return $response->content;
    }
}
