<?php

namespace App\Gateways\Sms;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use App\Gateways\Sms\Exceptions\CouldNotSendNotification;;

class SmspohApi extends Gateway
{
    protected HttpClient $client;

    protected string $endpoint;

    protected string $sender;

    protected mixed $token;

    public function __construct()
    {
        $this->token = settings('smspoh.token');
        $this->sender = settings('smspoh.sender');
        $this->client =  app(HttpClient::class);
        $this->endpoint = settings('smspoh.endpoint', 'https://smspoh.com/api/v2/send');
    }

    /**
     * Send text message.
     *
     * <code>
     * $message = [
     *   'sender'   => '',
     *   'to'       => '',
     *   'message'  => '',
     *   'test'     => '',
     * ];
     * </code>
     *
     * @link https://smspoh.com/rest-api-documentation/send?version=2
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     *
     * @throws CouldNotSendNotification
     */
    public function send(string $to, string $message)
    {
        try {
            $response = $this->client->request('POST', $this->endpoint, [
                'headers' => [
                    'Authorization' => "Bearer {$this->token}",
                ],
                'json' => [
                    'sender' => $this->sender,
                    'to' => $to,
                    'message' => $message,
                    'test' => false,
                ],
            ]);
            return json_decode((string) $response->getBody(), true);
        } catch (ClientException $e) {
            throw CouldNotSendNotification::gatewayRespondedWithAnError($e);
        } catch (GuzzleException $e) {
            throw CouldNotSendNotification::couldNotCommunicateWithGateway($e);
        }
    }
}
