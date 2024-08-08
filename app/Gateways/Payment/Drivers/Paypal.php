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
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Ixudra\Curl\Facades\Curl;

class Paypal implements Provider
{
    public static $baseUrl = 'https://api-m.sandbox.paypal.com/';
    public $name = 'paypal';
    public function __construct(
        public ?string $client_id = null,
        public ?string $client_secret = null
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
     * get the confguration for the gateway
     */
    public function getConfig(): Collection
    {
        $config = settings()->for('paypal');
        if ($config->count() == 0) {
            settings()->set('paypal.name', 'Paypal');
            settings()->set('paypal.logo', "https://www.paypalobjects.com/webstatic/icon/pp144.png");
            settings()->set('paypal.enable_withdraw', 'true');
            settings()->set('paypal.enable_deposit', 'true');
            settings()->set('paypal.currencies', 'USD,EUR');
            settings()->set('paypal.client_id', null);
            settings()->set('paypal.client_secret', null);
            $config = settings()->for('paypal');
        }
        return $config;
    }

    public function setConfig(Request $request): Collection
    {
        settings()->set('paypal.name', $request->name);
        settings()->set('paypal.logo', $request->logo);
        settings()->set('paypal.enable_withdraw', $request->boolean('enable_withdraw') ? 'true' : 'false');
        settings()->set('paypal.enable_deposit', $request->boolean('enable_deposit') ? 'true' : 'false');
        settings()->set('paypal.currencies', $request->currencies);
        settings()->set('paypal.client_id', $request->client_id);
        settings()->set('paypal.client_secret', $request->client_id);
        return settings()->for($this->name);
    }


    public static function url($path)
    {
        return static::$baseUrl . $path;
    }
    /**
     * Handle deposit request.
     *
     * @param  Deposit $deposit
     */
    public function deposit(Deposit $deposit)
    {
        $accessToken = static::accessToken();
        if (!$accessToken) throw ValidationException::withMessages(['gateway' => ['Paypal gateway is currently offline']]);
        $response = Curl::to(static::url('/v2/checkout/orders'))
            ->withBearer($accessToken)
            ->asJson()
            ->withData([
                "intent" => "CAPTURE",
                "application_context" => [
                    "return_url" => route('deposits.successfull', ['deposit' => $deposit->uuid]),
                    "cancel_url" => route('deposits.cancelled', ['deposit' => $deposit->uuid]),
                ],
                "purchase_units" => [
                    [
                        "reference_id" => $deposit->uuid,
                        "amount" => [
                            "currency_code" =>  $deposit->gateway_currency,
                            "value" => $deposit->gross_amount
                        ]
                    ]
                ]
            ])
            ->post();
        if (!isset($response->id)) throw ValidationException::withMessages(['gateway' => ['Paypal create tx failed. Contact admin']]);
        $deposit->remoteId = $response->id;
        $deposit->data = $response;
        $deposit->save();
        foreach ($deposit->links as $link) {
            if ($link->rel == 'approve') {
                return Inertia::location($link->href);
            }
        }
        if (!isset($response->id)) throw ValidationException::withMessages(['gateway' => ['Paypal Tx Approval Link not found. Contact admin']]);
    }

    private function accessToken()
    {
        /**
         * cache token fro 6 hours
         */
        return Cache::remember('paypal_access_token', 21600, fn () => $this->loadToken());
    }

    private function loadToken()
    {
        $response =  Curl::to(static::url('/v1/oauth2/token'))
            ->withOption(CURLOPT_HTTPAUTH, CURLAUTH_ANY)
            ->withOption(CURLOPT_USERPWD, "{$this->client_id}:{$this->client_secret}")
            ->withData([
                'grant_type' => 'client_credentials',
            ])
            ->post();
        return $response?->access_token;;
    }

    public function webhook(Request $request, string $type = 'deposit')
    {
        // Verify the webhook signature
        $webhookId = config('services.paypal.webhook_id'); // Store this in your configuration
        $requestBody = $request->getContent();
        $signatureVerification = $this->verifyWebhookSignature($webhookId, $requestBody, $request->header('PAYPAL-TRANSMISSION-ID'), $request->header('PAYPAL-TRANSMISSION-TIME'), $request->header('PAYPAL-TRANSMISSION-SIG'), $request->header('PAYPAL-CERT-URL'), $request->header('PAYPAL-AUTH-ALGO'));
        if ($signatureVerification->verification_status === 'SUCCESS') {
            // The webhook is verified, process it
            $eventType = $request->input('event_type');
            $resourceId = $request->input('resource.id');
            $resourceStatus = $request->input('resource.status');
            $amount = $request->input('resource.amount.total');
            $currency = $request->input('resource.amount.currency');
            if ($type === 'deposit' && $eventType === 'PAYMENT.CAPTURE.COMPLETED') {
                $deposit = Deposit::where('remoteId', $resourceId)->first();
                if ($deposit) {
                    $deposit->status = DepositStatus::COMPLETE;
                    $deposit->save();
                    if ($deposit->auto_approve) {
                        app(DepositTx::class)->create($deposit);
                    }
                    // You might want to trigger any post-deposit actions here
                }
            } elseif ($type === 'withdraw' && $eventType === 'PAYOUT.ITEM.COMPLETED') {
                $withdraw = Withdraw::where('remoteId', $resourceId)->first();
                if ($withdraw) {
                    $withdraw->status = WithdrawStatus::COMPLETE;
                    $withdraw->save();
                    // You might want to trigger any post-withdrawal actions here
                }
            }
            // Log the successful webhook
            \Log::info('PayPal Webhook Verified', [
                'type' => $type,
                'event_type' => $eventType,
                'resource_id' => $resourceId,
                'status' => $resourceStatus,
                'amount' => $amount,
                'currency' => $currency
            ]);
        } else {
            // Webhook invalid, log for manual investigation
            \Log::warning('Invalid PayPal Webhook', [
                'type' => $type,
                'request_body' => $requestBody
            ]);
        }

        return response('OK', 200);
    }

    private function verifyWebhookSignature($webhookId, $requestBody, $transmissionId, $transmissionTime, $transmissionSig, $certUrl, $authAlgo)
    {
        $accessToken = $this->accessToken();
        $response = Curl::to(static::url('/v1/notifications/verify-webhook-signature'))
            ->withBearer($accessToken)
            ->withData([
                'webhook_id' => $webhookId,
                'transmission_id' => $transmissionId,
                'transmission_time' => $transmissionTime,
                'transmission_sig' => $transmissionSig,
                'cert_url' => $certUrl,
                'auth_algo' => $authAlgo,
                'webhook_event' => json_decode($requestBody, true)
            ])
            ->asJson()
            ->post();

        return $response;
    }

    /**
     * Send funds to PayPal users.
     * @param  Collection $withdraws
     */
    public function withdraw(Collection $withdraws)
    {
        $accessToken = $this->accessToken();
        if (!$accessToken) {
            \Log::error('Failed to obtain PayPal access token for batch withdrawal');
            return;
        }
        $batchItems = [];
        foreach ($withdraws as $withdraw) {
            if ($withdraw->status != WithdrawStatus::APPROVED) continue;
            $batchItems[] = [
                'recipient_type' => 'EMAIL',
                'amount' => [
                    'value' => $withdraw->amount,
                    'currency' => $withdraw->fiat_currency
                ],
                'note' => 'Withdrawal from ' . config('app.name'),
                'sender_item_id' => $withdraw->uuid,
                'receiver' => $withdraw->to
            ];
        }

        if (empty($batchItems)) {
            \Log::info('No eligible withdrawals for batch processing');
            return;
        }

        $response = Curl::to(static::url('/v1/payments/payouts'))
            ->withBearer($accessToken)
            ->withData([
                'sender_batch_header' => [
                    'sender_batch_id' => 'Batch_' . uniqid(),
                    'email_subject' => 'You have a payout!',
                    'email_message' => 'You have received a payout from ' . config('app.name')
                ],
                'items' => $batchItems
            ])
            ->asJson()
            ->post();

        if (isset($response->batch_header->payout_batch_id)) {
            $batchId = $response->batch_header->payout_batch_id;
            // Process each withdrawal
            foreach ($withdraws as $withdraw) {
                if ($withdraw->status != WithdrawStatus::APPROVED) continue;
                $item = collect($response->items)->firstWhere('sender_item_id', $withdraw->uuid);
                if ($item && $item->transaction_status === 'SUCCESS') {
                    $withdraw->remoteId = $item->payout_item_id;
                    $withdraw->batch_id =  $batchId;
                    $withdraw->status = WithdrawStatus::COMPLETE;
                } else {
                    $withdraw->batch_id =  $batchId;
                    $withdraw->status = WithdrawStatus::PENDING;
                    $withdraw->gateway_error = $item->errors[0]->message ?? 'Unknown error';
                }
                $withdraw->save();
            }
        } else {
            \Log::error('PayPal batch payout failed', ['response' => $response]);
            foreach ($withdraws as $withdraw) {
                if ($withdraw->status != WithdrawStatus::APPROVED) continue;
                $withdraw->status = WithdrawStatus::FAILED;
                $withdraw->gateway_error = 'Batch payout creation failed';
                $withdraw->save();
            }
        }
    }

    /**
     * Check the status of a PayPal payout batch
     * @param string $batchId
     * @param Collection $withdraws
     */
    public function checkPayoutStatus($batchId, Collection $withdraws)
    {
        $accessToken = $this->accessToken();
        if (!$accessToken) {
            \Log::error('Failed to obtain PayPal access token for payout status check');
            return;
        }
        $response = Curl::to(static::url("/v1/payments/payouts/$batchId"))
            ->withBearer($accessToken)
            ->asJson()
            ->get();

        if (isset($response->batch_header->batch_status)) {
            foreach ($withdraws as $withdraw) {
                $item = collect($response->items)->firstWhere('payout_item_id', $withdraw->remoteId);
                if ($item) {
                    switch ($item->transaction_status) {
                        case 'SUCCESS':
                            $withdraw->status = WithdrawStatus::COMPLETE;
                            break;
                        case 'FAILED':
                        case 'RETURNED':
                        case 'BLOCKED':
                            $withdraw->status = WithdrawStatus::FAILED;
                            $withdraw->gateway_error = $item->errors[0]->message ?? 'Payout failed';
                            break;
                        case 'PENDING':
                        case 'UNCLAIMED':
                            $withdraw->status = WithdrawStatus::PENDING;
                            break;
                    }
                    $withdraw->save();
                }
            }
        } else {
            \Log::error('Failed to check PayPal payout status', ['response' => $response]);
        }
    }
}
