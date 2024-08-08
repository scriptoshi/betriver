<?php

namespace App\Gateways\Sms;

use Vonage\Client;
use Vonage\Client\Credentials\Basic;
use Vonage\SMS\Message\SMS;
use App\Gateways\Sms\Exceptions\CouldNotSendNotification;;
class Vonage extends Gateway
{


    /**
     * Vonage Api Key
     */
    protected string $apiKey;
    /**
     * Vonage api secret
     */
    protected string $apiSecret;
    /**
     * Vonage sender
     */
    protected string $sender;



    public function __construct()
    {
        $this->apiKey = settings('vonage.apiKey');
        $this->apiSecret = settings('vonage.apiSecret');
        $this->sender = settings('vonage.sms_sender');
    }

    /**
     * Send text message.
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     *
     * @throws CouldNotSendNotification
     */
    public function send(string $to, string $message): void
    {
        $basic  = new Basic($this->apiKey, $this->apiSecret);
        $client = new Client($basic);
        $response = $client->sms()->send(
            new SMS($to, $this->sender, $message)
        );
        $message = $response->current();
        if ($message->getStatus() != 0) {
            throw new CouldNotSendNotification("The message failed with status: " . $message->getStatus());
        }
    }
}
