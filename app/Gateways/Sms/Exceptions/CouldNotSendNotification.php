<?php

namespace App\Gateways\Sms\Exceptions;

use Exception;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

class CouldNotSendNotification extends Exception
{
    /**
     * Thrown when content length is greater than 918 characters.
     */
    public static function contentLengthLimitExceeded($count): self
    {
        return new self("Notification was not sent. Content length may not be greater than {$count} characters.", 422);
    }

    /**
     * Thrown when we're unable to communicate with gateway.
     */
    public static function gatewayRespondedWithAnError(ClientException $exception): self
    {
        if (!$exception->hasResponse()) {
            return new self('Gateway responded with an error but no response body found');
        }

        return new self("Gateway responded with an error '{$exception->getCode()} : {$exception->getMessage()}'", $exception->getCode(), $exception);
    }

    /**
     * Thrown when we're unable to communicate with gateway.
     */
    public static function couldNotCommunicateWithGateway(Exception|GuzzleException $exception): self
    {
        return new self("The communication with gateway failed. Reason: {$exception->getMessage()}", $exception->getCode(), $exception);
    }
}
