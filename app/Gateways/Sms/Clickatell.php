<?php

namespace App\Gateways\Sms;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use App\Gateways\Sms\Exceptions\CouldNotSendNotification;;

use Ixudra\Curl\Facades\Curl;

class Clickatell extends Gateway
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


    public function __construct()
    {
        $this->apikey = settings('clickatell.apiKey');
        $this->endpoint = "https://platform.clickatell.com/messages/http/send";
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
            ->withData([
                'apiKey' => $this->apikey,
                'to' => $to,
                'message' => $message
            ])
            ->asJson()
            ->get();
        if ($response->errorCode != static::SUCCESSFUL_SEND) {
            throw new CouldNotSendNotification(static::errorMessage($response->errorCode));
        }
    }

    public static function errorMessage($code): string
    {
        return match ($code) {
            static::SUCCESSFUL_SEND => __('Clickatell Send successfull'),
            static::AUTH_FAILED => __('Clickatell Auth Failed'),
            static::INVALID_DEST_ADDRESS => __('Clickatell Invalid destination Address'),
            static::INVALID_API_ID => __('Clickatell Invalid API KEY'),
            static::CANNOT_ROUTE_MESSAGE => __('Clickatell Cannot route Message'),
            static::DEST_MOBILE_BLOCKED => __('Destination Phone number blocked'),
            static::DEST_MOBILE_OPTED_OUT => __('Destination Phone number opted out'),
            static::MAX_MT_EXCEEDED => __('Max MT Message Exceeded'),
            static::NO_CREDIT_LEFT => __('Clickatell Low balance'),
            static::INTERNAL_ERROR => __('Clickatell Internal Error'),
        };
    }
}
