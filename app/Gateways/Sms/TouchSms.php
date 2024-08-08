<?php

namespace App\Gateways\Sms;

use App\Gateways\Sms\Exceptions\CouldNotSendNotification;
use TouchSms\ApiClient\Api\Model\OutboundMessage;
use TouchSms\ApiClient\Api\Model\SendMessageBody;
use TouchSms\ApiClient\Client;
use TouchSms\ApiClient\ClientFactory;

class TouchSms extends Gateway
{
    /** @var Client */
    private Client $client;
    protected string $sender;

    public function __construct()
    {
        $token_id = settings('touchsms.token_id');
        $token_id = settings('touchsms.token_id');
        $sender = settings('touchsms.default_sender', 'SHARED_NUMBER');
        $access_token = settings('touchsms.access_token');
        if (empty($token_id) || empty($access_token) || empty($sender))
            throw new \InvalidArgumentException('Missing TouchSMS config in services');
        $this->client = ClientFactory::create($token_id, $access_token);
        $this->sender = $sender;
    }

    public function send(string $to, string $message): void
    {
        $apiMessage = (new OutboundMessage())
            ->setTo($to)
            ->setFrom($this->sender)
            ->setBody($message);
        $response = $this->client->sendMessages(
            (new SendMessageBody())->setMessages([$apiMessage])
        );
        if (!$response || count($response->getData()->getErrors())) {
            $error = $response->getData()->getErrors()[0];
            throw new CouldNotSendNotification($error->getErrorCode() . ($error->getErrorHelp() ? ' - ' . $error->getErrorHelp() : ''), 400);
        }
    }
}
