<?php

namespace App\Gateways\Payment\Drivers;

use App\Enums\DepositStatus;
use App\Enums\WithdrawStatus;
use App\Gateways\Payment\Contracts\Provider;
use Inertia\Inertia;
use App\Gateways\Payment\Actions\DepositTx;
use App\Models\Currency;
use App\Models\Deposit;
use App\Models\Withdraw;
use App\Support\Rate;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Ixudra\Curl\Facades\Curl;
use Str;

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
    ) {}

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
            settings()->set('payeer.auto_approve_withdraw_address', 'true');
            settings()->set('payeer.enable_deposit', 'true');
            settings()->set('payeer.shop', null);
            settings()->set('payeer.merchant_key', null);
            settings()->set('payeer.accountNumber', null);
            settings()->set('payeer.apiId', null);
            settings()->set('payeer.apiSecret', null);
            settings()->set('payeer.max_withdraw_limit', 0);
            settings()->set('payeer.min_withdraw_limit', 15);
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
        settings()->set('payeer.auto_approve_withdraw_address', $request->boolean('auto_approve_withdraw_address') ? 'true' : 'false');
        settings()->set('payeer.enable_deposit', $request->boolean('enable_deposit') ? 'true' : 'false');
        settings()->set('payeer.shop', $request->shop);
        settings()->set('payeer.merchant_key', $request->merchant_key);
        settings()->set('payeer.accountNumber', $request->accountNumber);
        settings()->set('payeer.apiId', $request->apiId);
        settings()->set('payeer.apiSecret', $request->apiSecret);
        settings()->set('payeer.max_withdraw_limit', $request->max_withdraw_limit);
        settings()->set('payeer.min_withdraw_limit', $request->min_withdraw_limit);
        return settings()->for('payeer');
    }


    /**
     * update currencies supported by gateway
     */
    public function updateCurrencies()
    {
        $rates = Rate::symbols();
        collect(['USD' => 'US Dollar', 'EUR' => 'Euro'])
            ->each(function ($name, $symbol) use ($rates) {
                Currency::query()->updateOrCreate([
                    "code" => $symbol,
                    'gateway' => $this->name,
                ], [
                    "name" =>  $name,
                    "logo_url" => $symbol,
                    'rate' => $rates[$symbol],
                    "precision" => 2
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
        $m_amount = number_format($deposit->gateway_amount, 2, '.', '');
        $m_shop = $this->shop;
        $m_order_id = $deposit->uuid;
        $m_curr = $deposit->gateway_currency;
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
        $params = $request->all();
        // Validate the presence of necessary parameters
        if (!$this->validatePayeerParams($params)) {
            return response('Invalid parameters', 400);
        }
        // Verify the IPN signature
        if (!$this->verifyPayeerSignature($params)) {
            return response('Signature mismatch', 400);
        }
        $deposit = Deposit::where('uuid', $request->m_orderid)->first();
        if ($deposit->status == DepositStatus::COMPLETE)
            return response('IPN received', 200);
        if (
            $params['m_status'] === 'success' &&
            $request->m_amount >= $deposit->gateway_amount &&
            $params['m_curr'] == $deposit->gateway_currency
        ) {
            // Payment is successful, update the order status in your database
            $deposit->status = DepositStatus::COMPLETE;
            $deposit->gateway_error = null;
            $deposit->save();
            // Payment is successful, lets proceed to update users balance.
            app(DepositTx::class)->create($deposit);
        } else {
            // Payment not successful, log or handle accordingly
            $deposit->status = DepositStatus::FAILED;
            $deposit->gateway_error = value(function () use ($params, $deposit) {
                if ($params['m_status'] !== 'success') return __('Gateway status not successful');
                if ($params['m_amount'] < $deposit->gateway_amount) return __('Low Deposit amount recieved. Contact admin');
                return __('Deposit currency mismatch');
            });
            $deposit->save();
        }

        return response('IPN received', 200);
    }

    /**
     * Update the status of a withdraw (payout) at CoinPayments.
     *
     * @param string $withdrawId The ID of the withdraw in our system
     * @return bool Returns true if the update was successful, false otherwise
     */
    public function updateWithdrawStatus(Withdraw $withdraw): bool
    {
        return false;
    }



    /**
     * send funds to payeer user.
     * Payeer withdrawals only support USD for now
     * @param  Withdraw $withdraw
     */
    public function withdraw(Collection $withdraws)
    {

        $batchId = Str::random(32);
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
                $withdraw->batchId = $batchId;
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


    public static function failDeposit(Deposit $deposit, string $error)
    {
        $deposit->status = DepositStatus::FAILED;
        $deposit->gateway_error = $error;
        $deposit->save();
        return redirect()->route('deposits.show', $deposit)->with('error', $error);
    }
    /**
     * 
     */

    public function returned(Request $request, Deposit $deposit)
    {
        $params = $request->all();
        // override url
        $deposit = Deposit::where('uuid', $request->m_orderid)->first();
        // Check that all necessary parameters are present
        if (!$this->validatePayeerParams($params)) {
            return static::failDeposit($deposit, __('Invalid payment parameters.'));
        }
        // Verify the payment signature
        if (!$this->verifyPayeerSignature($params)) {
            return static::failDeposit($deposit, __('Payment verification failed.'));
        }
        // Check the payment status
        if ($params['m_status'] === 'success') {
            $deposit->status = DepositStatus::COMPLETE;
            $deposit->gateway_error = null;
            $deposit->save();
            // Payment is successful, lets proceed to update users balance.
            app(DepositTx::class)->create($deposit);
            return redirect()->route('payment.success')->with('success', __('Payment completed successfully.'));
        } else {
            // Payment not successful
            return static::failDeposit($deposit, __('Payment not successful.'));
        }
    }

    public function validatePayeerParams($params)
    {
        $requiredParams = [
            'm_operation_id',
            'm_operation_ps',
            'm_operation_date',
            'm_operation_pay_date',
            'm_shop',
            'm_orderid',
            'm_amount',
            'm_curr',
            'm_desc',
            'm_status',
            'm_sign'
        ];

        foreach ($requiredParams as $param) {
            if (!isset($params[$param])) {
                return false;
            }
        }

        return true;
    }

    public function verifyPayeerSignature($params)
    {
        $secret_key =  $this->merchant_key;  // The secret key from Payeer
        $signParams = [
            $params['m_operation_id'],
            $params['m_operation_ps'],
            $params['m_operation_date'],
            $params['m_operation_pay_date'],
            $params['m_shop'],
            $params['m_orderid'],
            $params['m_amount'],
            $params['m_curr'],
            $params['m_desc'],
            $params['m_status']
        ];
        // Create the hash to compare with m_sign
        $sign = strtoupper(hash('sha256', implode(':', $signParams) . ':' . $secret_key));
        return $sign === $params['m_sign'];
    }
}
