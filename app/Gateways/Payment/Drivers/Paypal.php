<?php

namespace App\Gateways\Payment\Drivers;

use Illuminate\Support\Facades\Http;
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
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Support\Facades\Log;
use Str;

class Paypal implements Provider
{
    public static $baseUrl = 'https://api-m.sandbox.paypal.com';
    public static $sandboxUrl = 'https://api-m.sandbox.paypal.com';
    public $name = 'paypal';
    public function __construct(
        public ?string $client_id = null,
        public ?string $client_secret = null
    ) {}

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
            settings()->set('paypal.auto_approve_withdraw_address', 'true');
            settings()->set('paypal.enable_deposit', 'true');
            settings()->set('paypal.client_id', null);
            settings()->set('paypal.client_secret', null);
            settings()->set('paypal.webhook_id', null);
            settings()->set('paypal.max_withdraw_limit', 10);
            settings()->set('paypal.min_withdraw_limit', 5000);
            $config = settings()->for('paypal');
        }
        return $config;
    }

    public function setConfig(Request $request): Collection
    {
        settings()->set('paypal.name', $request->name);
        settings()->set('paypal.logo', $request->logo);
        settings()->set('paypal.enable_withdraw', $request->boolean('enable_withdraw') ? 'true' : 'false');
        settings()->set('paypal.auto_approve_withdraw_address', $request->boolean('auto_approve_withdraw_address') ? 'true' : 'false');
        settings()->set('paypal.enable_deposit', $request->boolean('enable_deposit') ? 'true' : 'false');
        settings()->set('paypal.client_id', $request->client_id);
        settings()->set('paypal.client_secret', $request->client_secret);
        settings()->set('paypal.webhook_id', $request->webhook_id);
        settings()->set('paypal.max_withdraw_limit', $request->max_withdraw_limit);
        settings()->set('paypal.min_withdraw_limit', $request->min_withdraw_limit);
        return settings()->for($this->name);
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
                    'rate' => $rates[$symbol] ?? null,
                    "precision" => 2
                ]);
            });
    }


    public static function url($path)
    {
        if (config('app.sandbox'))
            return static::$sandboxUrl . $path;
        return static::$baseUrl . $path;
    }
    /**
     * Handle deposit request.
     *
     * @param  Deposit $deposit
     */
    public function deposit(Deposit $deposit)
    {
        $accessToken = $this->accessToken();
        if (!$accessToken) throw ValidationException::withMessages(['gateway' => ['Paypal gateway is currently offline']]);

        $response = Curl::to(static::url('/v2/checkout/orders'))
            ->withBearer($accessToken)
            ->asJson()
            ->withData([
                "intent" => "CAPTURE",
                "application_context" => [
                    "return_url" => route('deposits.return', $deposit),
                    "cancel_url" => route('deposits.cancel', $deposit),
                ],
                "purchase_units" => [
                    [
                        "reference_id" => $deposit->uuid,
                        "amount" => [
                            "currency_code" =>  $deposit->gateway_currency,
                            "value" => $deposit->gateway_amount
                        ]
                    ]
                ]
            ])
            ->post();
        if (!isset($response->id)) throw ValidationException::withMessages(['gateway' => ['Paypal create tx failed. Contact admin']]);
        $deposit->remoteId = $response->id;
        $deposit->data = $response;
        $deposit->status = DepositStatus::PROCESSING;
        $deposit->save();
        foreach ($response->links as $link) {
            if ($link->rel == 'approve') {
                return Inertia::location($link->href);
            }
        }
        if (!isset($response->id)) throw ValidationException::withMessages(['gateway' => ['Paypal Tx Approval Link not found. Contact admin']]);
    }

    /**
     * Manually check the status of a deposit
     * 
     * @param Deposit $deposit
     * @return bool Returns true if the status was successfully updated, false otherwise
     */
    public function checkDepositStatus(Deposit $deposit)
    {
        // Skip if no remote ID exists
        if (!$deposit->remoteId) {
            Log::error('No remote ID found for deposit: ' . $deposit->id);
            return false;
        }

        try {
            $accessToken = $this->accessToken();
            if (!$accessToken) {
                Log::error('Failed to obtain PayPal access token for deposit status check');
                return false;
            }

            // Get order details from PayPal
            $response = Http::withToken($accessToken)
                ->withHeaders([
                    'Content-Type' => 'application/json'
                ])
                ->get(static::url("/v2/checkout/orders/{$deposit->remoteId}"));

            if (!$response->successful()) {
                Log::error('Failed to fetch PayPal order status', [
                    'deposit_id' => $deposit->id,
                    'remote_id' => $deposit->remoteId,
                    'response' => $response->body()
                ]);
                return false;
            }

            $orderDetails = $response->json();

            // Map PayPal status to our deposit status
            switch ($orderDetails['status']) {
                case 'COMPLETED':
                    if ($deposit->status !== DepositStatus::COMPLETE) {
                        $deposit->status = DepositStatus::COMPLETE;
                        $deposit->save();
                        // Process the completed deposit
                        app(DepositTx::class)->create($deposit);
                    }
                    break;

                case 'APPROVED':
                    // Payment approved but not captured
                    if ($deposit->status !== DepositStatus::PROCESSING) {
                        $deposit->status = DepositStatus::PROCESSING;
                        $deposit->save();
                        // Attempt to capture the payment
                        $this->captureOrder($deposit->remoteId);
                    }
                    break;

                case 'VOIDED':
                case 'EXPIRED':
                    if ($deposit->status !== DepositStatus::FAILED) {
                        $deposit->status = DepositStatus::FAILED;
                        $deposit->gateway_error = 'Order ' . strtolower($orderDetails['status']);
                        $deposit->save();
                    }
                    break;

                case 'CREATED':
                case 'SAVED':
                    if ($deposit->status !== DepositStatus::PENDING) {
                        $deposit->status = DepositStatus::PENDING;
                        $deposit->save();
                    }
                    break;

                default:
                    Log::warning('Unknown PayPal order status', [
                        'deposit_id' => $deposit->id,
                        'remote_id' => $deposit->remoteId,
                        'status' => $orderDetails['status']
                    ]);
                    return false;
            }

            // Store the complete response in the deposit data
            $deposit->data = $orderDetails;
            $deposit->save();

            return true;
        } catch (\Exception $e) {
            Log::error('Error checking PayPal deposit status', [
                'deposit_id' => $deposit->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    private function accessToken()
    {
        /**
         * cache token fro 6 hours
         */
        return   Cache::remember('__paypal_access_token', 21600, fn() => $this->loadToken());
    }



    private function loadToken()
    {
        $clientId = $this->client_id;
        $clientSecret = $this->client_secret;
        $response = Http::withBasicAuth($clientId, $clientSecret)
            ->asForm()
            ->post(static::url('/v1/oauth2/token'), [
                'grant_type' => 'client_credentials',
            ]);

        if ($response->successful()) {
            return $response->json()['access_token'];
        }

        throw new \Exception('Unable to retrieve PayPal access token: ' . $response->body());
    }


    public static function failDeposit(Deposit $deposit, string $error)
    {
        $deposit->status = DepositStatus::FAILED;
        $deposit->gateway_error = $error;
        $deposit->save();
        return redirect()->route('deposits.show', $deposit)->with('error', $error);
    }

    public function returned(Request $request, Deposit $deposit)
    {
        $orderID = $request->query('token');
        $accessToken = $this->accessToken();
        // override
        $deposit =  Deposit::where('remoteId', $orderID)->first();
        // attempt to capture

        $response = Http::withToken($accessToken)
            ->withHeaders([
                'Content-Type' => 'application/json'
            ])
            ->withBody('', 'application/json')  // Empty body as a string
            ->post(static::url("/v2/checkout/orders/{$orderID}/capture"));
        if ($response->successful()) {
            $orderDetails = $response->json();
            if ($orderDetails['status'] === 'COMPLETED') {
                // Payment is successful
                $this->updateOrderStatus($orderID, DepositStatus::COMPLETE);
                return redirect()->route('deposits.show', $deposit)->with('success', 'Deposit completed successfully.');
            } else {
                // Payment not completed
                return static::failDeposit($deposit, __('Payment not completed.'));
            }
        } else {
            // Payment capture failed
            return static::failDeposit($deposit, __('Payment capture failed.'));
        }
    }




    /**
     * Handle incomming webhooks from paypal
     */
    public function webhook(Request $request, string $type = 'deposit')
    {
        $webhookData = $request->all();
        // Log the webhook for debugging purposes
        Log::info('PayPal Webhook Received:', $webhookData);
        // Validate the webhook signature (optional but recommended)
        if (!$this->validateWebhook($request)) {
            return response('Invalid signature', 400);
        }

        // Handle different event types
        switch ($webhookData['event_type']) {
            case 'CHECKOUT.ORDER.APPROVED':
                $this->handleOrderApproved($webhookData);
                break;

            case 'PAYMENT.CAPTURE.COMPLETED':
                $this->handlePaymentCompleted($webhookData);
                break;

            case 'PAYMENT.CAPTURE.DENIED':
                $this->handlePaymentDenied($webhookData);
                break;

            case 'PAYMENT.CAPTURE.PENDING':
                $this->handlePaymentPending($webhookData);
                break;

            case 'PAYMENT.CAPTURE.REFUNDED':
                $this->handlePaymentRefunded($webhookData);
                break;

            case 'PAYMENT.CAPTURE.REVERSED':
                $this->handlePaymentReversed($webhookData);
                break;
                // I should add more cases as needed
            default:
                Log::info('Unhandled event type: ' . $webhookData['event_type']);
                break;
        }

        // Respond to PayPal to acknowledge receipt of the webhook
        return response('Webhook received', 200);
    }

    protected function handleOrderApproved($webhookData)
    {
        $orderId = $webhookData['resource']['id'];
        // Capture the payment using the order ID
        $this->captureOrder($orderId);
    }

    protected function handlePaymentCompleted($webhookData)
    {
        $orderId = $webhookData['resource']['supplementary_data']['related_ids']['order_id'];
        // Update your database to reflect the payment completion
        $this->updateOrderStatus($orderId, DepositStatus::COMPLETE);
    }

    protected function handlePaymentDenied($webhookData)
    {
        $orderId = $webhookData['resource']['supplementary_data']['related_ids']['order_id'];
        // Update your database to reflect the payment denial
        $this->updateOrderStatus($orderId, DepositStatus::FAILED);
    }

    protected function handlePaymentPending($webhookData)
    {
        $orderId = $webhookData['resource']['supplementary_data']['related_ids']['order_id'];
        // Update your database to reflect the pending status
        $this->updateOrderStatus($orderId, DepositStatus::PENDING);
    }

    protected function handlePaymentRefunded($webhookData)
    {
        $refundId = $webhookData['resource']['id'];
        $orderId = $webhookData['resource']['supplementary_data']['related_ids']['order_id'];
        // Update your database to reflect the refund
        $this->updateOrderStatus($orderId, DepositStatus::REFUNDED);
    }

    protected function handlePaymentReversed($webhookData)
    {
        $reversalId = $webhookData['resource']['id'];
        $orderId = $webhookData['resource']['supplementary_data']['related_ids']['order_id'];
        // Update your database to reflect the reversal
        $this->updateOrderStatus($orderId, DepositStatus::REVERSED);
    }

    // This function captures the order payment. 
    protected function captureOrder($orderId)
    {
        $accessToken = $this->accessToken(); // Reuse the function to get access token
        $response = Http::withToken($accessToken)
            ->withHeaders([
                'Content-Type' => 'application/json'
            ])
            ->withBody('', 'application/json')  // Empty body as a string
            ->post(static::url("/v2/checkout/orders/{$orderId}/capture"));
        if ($response->successful()) {
            $orderDetails = $response->json();
            if ($orderDetails['status'] === 'COMPLETED') {
                // Payment captured successfully, update the order status
                $this->updateOrderStatus($orderId, DepositStatus::COMPLETE);
            } else {
                // Handle other statuses if necessary
                $this->updateOrderStatus($orderId, DepositStatus::FAILED, 'Order capture not completed. Order ID: ' . $orderId);
                Log::warning('Order capture not completed. Order ID: ' . $orderId);
            }
        } else {
            // Handle capture failure
            $this->updateOrderStatus($orderId, DepositStatus::FAILED, 'Order capture failed. Order ID: ' . $orderId);
            Log::error('Order capture failed. Order ID: ' . $orderId);
        }
    }

    // This function updates the order status in database
    protected function updateOrderStatus($orderId, DepositStatus $status, $message = null)
    {
        // Update  order status in the database
        $deposit = Deposit::where('remoteId', $orderId)->first();
        if (!$deposit) {
            Log::error('No deposit found for orderID ' . $orderId);
            return;
        }
        if ($deposit && $status != $deposit->status) {
            $deposit->status = $status;
            $deposit->gateway_error = $message;
            $deposit->save();
            if ($status ==  DepositStatus::COMPLETE) {
                app(DepositTx::class)->create($deposit);
            }
        }
    }

    protected function validateWebhook(Request $request)
    {
        $headers = $request->headers->all();
        $body = $request->getContent();

        $webhookId = env('PAYPAL_WEBHOOK_ID');  // Retrieve your Webhook ID from PayPal
        $signatureVerificationUrl = static::url('/v1/notifications/verify-webhook-signature');

        $response = Http::withToken($this->accessToken())
            ->post($signatureVerificationUrl, [
                'transmission_id' => $headers['paypal-transmission-id'][0],
                'transmission_time' => $headers['paypal-transmission-time'][0],
                'cert_url' => $headers['paypal-cert-url'][0],
                'auth_algo' => $headers['paypal-auth-algo'][0],
                'transmission_sig' => $headers['paypal-transmission-sig'][0],
                'webhook_id' => $webhookId,
                'webhook_event' => json_decode($body, true),
            ]);

        return $response->successful() && $response->json()['verification_status'] === 'SUCCESS';
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
                    'value' => $withdraw->gateway_amount,
                    'currency' => $withdraw->gateway_currency
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
        $batchId  = Str::random(32);
        $response = Curl::to(static::url('/v1/payments/payouts'))
            ->withBearer($accessToken)
            ->withData([
                'sender_batch_header' => [
                    'sender_batch_id' => $batchId,
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
                    $withdraw->batchId =  $batchId;
                    $withdraw->status = WithdrawStatus::COMPLETE;
                } else {
                    $withdraw->batchId =  $batchId;
                    $withdraw->status = WithdrawStatus::PENDING;
                    $withdraw->gateway_error = $item->errors[0]->message ?? 'Unknown error';
                }
                $withdraw->save();
                $withdraw->notify();
            }
        } else {
            \Log::error('PayPal batch payout failed', ['response' => $response]);
            foreach ($withdraws as $withdraw) {
                if ($withdraw->status != WithdrawStatus::APPROVED) continue;
                $withdraw->status = WithdrawStatus::FAILED;
                $withdraw->gateway_error = 'Batch payout creation failed';
                $withdraw->save();
                $withdraw->notify();
            }
        }
    }




    /**
     * Update the status of a withdraw (payout) at Paypal.
     *
     * @param Withdraw $withdraw the withdraw in our system
     * @return bool Returns true if the update was successful, false otherwise
     */
    public function updateWithdrawStatus(Withdraw $withdraw): bool
    {

        $batchId = $withdraw->batchId;
        $withdraws = Withdraw::query()->where('batchId', $batchId)->get();
        $accessToken = $this->accessToken();
        if (!$accessToken) {
            \Log::error('Failed to obtain PayPal access token for payout status check');
            return false;
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
            return false;
        }
        return true;
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
