<?php

namespace App\Gateways\Payment\Drivers;

use App\Enums\DepositStatus;
use App\Enums\WithdrawStatus;
use App\Gateways\Payment\Contracts\Provider;
use App\Models\Currency;
use App\Models\Deposit;
use App\Models\Withdraw;
use App\Support\Rate;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class Skrill implements Provider
{
    public string $name = 'skrill';
    private string $apiUrl = 'https://pay.skrill.com';
    private string $merchantId;
    private string $secretWord;

    public function __construct()
    {
        $this->merchantId = settings('skrill.merchant_id');
        $this->secretWord = settings('skrill.secret_word');
    }


    /**
     * Get the name of the payment gateway.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Retrieve or set up default configuration for Skrill.
     *
     * @return Collection
     */
    public function getConfig(): Collection
    {
        $config = settings()->for('skrill');
        if ($config->count() == 0) {
            settings()->set('skrill.name', 'Skrill');
            settings()->set('skrill.logo', "https://www.skrill.com/typo3conf/ext/sitepackage/Resources/Public/images/Skrill-Logo.svg");
            settings()->set('skrill.enable_withdraw', 'true');
            settings()->set('skrill.auto_approve_withdraw_address', 'true');
            settings()->set('skrill.enable_deposit', 'true');
            settings()->set('skrill.merchant_id', null);
            settings()->set('skrill.secret_word', null);
            settings()->set('skrill.max_withdraw_limit', 0);
            settings()->set('skrill.min_withdraw_limit', 10);
            $config = settings()->for('skrill');
        }
        return $config;
    }

    /**
     * Save the configuration settings for Skrill.
     *
     * @param Request $request
     * @return Collection
     */
    public function setConfig(Request $request): Collection
    {
        settings()->set('skrill.name', $request->name);
        settings()->set('skrill.logo', $request->logo);
        settings()->set('skrill.enable_withdraw', $request->boolean('enable_withdraw') ? 'true' : 'false');
        settings()->set('skrill.auto_approve_withdraw_address', $request->boolean('auto_approve_withdraw_address') ? 'true' : 'false');
        settings()->set('skrill.enable_deposit', $request->boolean('enable_deposit') ? 'true' : 'false');
        settings()->set('skrill.merchant_id', $request->merchant_id);
        settings()->set('skrill.secret_word', $request->secret_word);
        settings()->set('skrill.max_withdraw_limit', $request->max_withdraw_limit);
        settings()->set('skrill.min_withdraw_limit', $request->min_withdraw_limit);
        return settings()->for($this->name);
    }

    /**
     * update currencies supported by gateway
     */
    public function updateCurrencies()
    {
        $rates = Rate::symbols();
        collect(['USD' => 'US Dollars', 'GBP' => 'Pound Sterling'])
            ->each(function ($name, $symbol) use ($rates) {
                Currency::query()->updateOrCreate([
                    "code" => $symbol,
                    'gateway' => $this->name,
                ], [
                    "name" =>  $name,
                    "logo_url" => $symbol,
                    "rate" => $rates[$symbol],
                ]);
            });
    }

    /**
     * Initiate a deposit transaction with Skrill.
     *
     * @param Deposit $deposit
     * @return RedirectResponse
     * @throws \Exception
     */
    public function deposit(Deposit $deposit): RedirectResponse
    {
        $params = [
            'pay_to_email' => $this->merchantId,
            'transaction_id' => $deposit->uuid,
            'return_url' => route('deposits.return', $deposit),
            'cancel_url' => route('deposits.cancel', $deposit),
            'status_url' => route('deposits.webhook', ['provider' => 'skrill']),
            'language' => 'EN',
            'amount' => $deposit->gross_amount,
            'currency' => $deposit->gateway_currency,
            'detail1_description' => 'Deposit to ' . config('app.name'),
            'detail1_text' => $deposit->uuid,
        ];
        $response = Http::asForm()->post($this->apiUrl, $params);
        if ($response->successful()) {
            $deposit->remoteId = $deposit->uuid;
            $deposit->data = $response->json();
            $deposit->status = DepositStatus::PROCESSING;
            $deposit->save();
            return Inertia::location($response['redirect_url']);
        } else {
            return back()->with('error', 'Failed to initiate Skrill deposit: ' . $response->body());
        }
    }

    /**
     * Handle incoming webhooks from Skrill to update transaction statuses.
     *
     * @param Request $request
     * @param string $type
     * @return \Illuminate\Http\Response
     */
    public function webhook(Request $request, string $type = 'deposit'): \Illuminate\Http\Response
    {
        $data = $request->all();
        Log::info('Skrill Webhook Received:', $data);

        if (!$this->validateSignature($data)) {
            return response('Invalid signature', 400);
        }

        $deposit = Deposit::where('uuid', $data['transaction_id'])->first();
        if (!$deposit) {
            Log::error('No deposit found for transaction_id: ' . $data['transaction_id']);
            return response('Deposit not found', 404);
        }

        switch ($data['status']) {
            case '2':
                $deposit->status = DepositStatus::COMPLETE;
                break;
            case '0':
            case '-1':
            case '-2':
                $deposit->status = DepositStatus::FAILED;
                $deposit->gateway_error = 'Payment failed or cancelled';
                break;
            case '-3':
                $deposit->status = DepositStatus::REFUNDED;
                break;
            default:
                $deposit->status = DepositStatus::PENDING;
        }

        $deposit->save();

        return response('OK', 200);
    }

    /**
     * Process withdrawal requests to Skrill.
     *
     * @param Collection $withdraws
     * @return void
     */
    public function withdraw(Collection $withdraws): void
    {
        foreach ($withdraws as $withdraw) {
            if ($withdraw->status != WithdrawStatus::APPROVED) continue;

            $params = [
                'action' => 'prepare',
                'email' => $this->merchantId,
                'password' => md5($this->secretWord),
                'amount' => $withdraw->gateway_amount,
                'currency' => $withdraw->gateway_currency, // check if conversions are needed
                'bnf_email' => $withdraw->to,
                'transaction_id' => $withdraw->uuid,
                'subject' => 'Withdrawal from ' . config('app.name'),
                'note' => 'Withdrawal reference: ' . $withdraw->uuid,
            ];

            $response = Http::asForm()->post($this->apiUrl . '/app/pay', $params);

            if ($response->successful()) {
                $sid = $response['sid'];
                $transferResponse = Http::asForm()->post($this->apiUrl . '/app/pay', [
                    'action' => 'transfer',
                    'sid' => $sid,
                ]);

                if ($transferResponse->successful()) {
                    $withdraw->remoteId = $transferResponse['transaction_id'];
                    $withdraw->status = WithdrawStatus::COMPLETE;
                } else {
                    $withdraw->status = WithdrawStatus::FAILED;
                    $withdraw->gateway_error = 'Transfer failed: ' . $transferResponse->body();
                }
            } else {
                $withdraw->status = WithdrawStatus::FAILED;
                $withdraw->gateway_error = 'Preparation failed: ' . $response->body();
            }

            $withdraw->save();
        }
    }

    /**
     * Handle the user's return after a deposit (mostly for redirection).
     *
     * @param Request $request
     * @param Deposit $deposit
     * @return RedirectResponse
     */
    public function returned(Request $request, Deposit $deposit): RedirectResponse
    {
        // Skrill doesn't typically require additional processing on return
        // The status should be updated via the webhook
        return redirect()->route('deposits.show', $deposit);
    }

    /**
     * Validate the signature of incoming webhook data.
     *
     * @param array $data
     * @return bool
     */
    private function validateSignature(array $data): bool
    {
        $concatFields = $data['merchant_id']
            . $data['transaction_id']
            . strtoupper(md5($this->secretWord))
            . $data['mb_amount']
            . $data['mb_currency']
            . $data['status'];

        $calculatedSignature = strtoupper(md5($concatFields));

        return $calculatedSignature === $data['md5sig'];
    }

    /**
     * Update the status of a withdraw (payout) at skrill.
     *
     * @param Withdraw $withdraw the withdraw in our system
     * @return bool Returns true if the update was successful, false otherwise
     */
    public function updateWithdrawStatus(Withdraw $withdraw): bool
    {
        return false;
    }
}
