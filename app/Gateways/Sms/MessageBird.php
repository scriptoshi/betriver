<?php

namespace App\Gateways\Sms;

use App\Gateways\Sms\Exceptions\CouldNotSendNotification;;

use Ixudra\Curl\Facades\Curl;

class MessageBird extends Gateway
{

    /**
     * The sms send endpoiny
     */
    protected string $endpoint;
    /**
     * sender
     */
    protected string $originator;
    /**
     * api key
     */
    protected string $access_key;


    public function __construct()
    {
        $this->access_key = settings('messagebird.access_key');
        $this->originator = settings('messagebird.originator');
        $this->endpoint = "https://rest.messagebird.com/messages";
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
        $response = Curl::to($this->endpoint)
            ->withAuthorization("AccessKey {$this->access_key}")
            ->withData([
                'originator' => $this->originator,
                'recipients' => $to,
                'body' => $message
            ])
            ->returnResponseObject()
            ->asJson()
            ->post();
        if (isset($response->error)) {
            throw new CouldNotSendNotification($response->error);
        }
    }
}
