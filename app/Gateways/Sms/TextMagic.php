<?php

namespace App\Gateways\Sms;

use App\Gateways\Sms\Exceptions\CouldNotSendNotification;;

use Ixudra\Curl\Facades\Curl;

class TextMagic extends Gateway
{

    const SUCCESSFUL_SEND = 0;
    const AUTH_FAILED = 1;
    const INVALID_DEST_ADDRESS = 105;
    const INVALID_API_ID = 108;
    const CANNOT_ROUTE_MESSAGE = 114;
    const DEST_MOBILE_BLOCKED = 121;
    const DEST_MOBILE_OPTED_OUT = 122;
    const MAX_MT_EXCEEDED = 130;
    const NO_CREDIT_LEFT = 301;
    const INTERNAL_ERROR = 901;

    protected string $endpoint;

    protected string $apikey;
    protected string $username;


    public function __construct()
    {
        $this->apikey = settings('textmagic.apiv2_key');
        $this->username = settings('textmagic.username');
        $this->endpoint = "https://rest.textmagic.com/api/v2/messages";
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
            ->withHeaders([
                'X-TM-Username' => $this->username,
                'X-TM-Key' => $this->apikey,
            ])
            ->withData([
                'phones' => $to,
                'text' => $message
            ])
            ->returnResponseObject()
            ->asJson()
            ->post();
        if ($response->status  >= 300 || $response->error) {
            throw new CouldNotSendNotification('Bad or Unauthorized Request');
        }
    }
}
