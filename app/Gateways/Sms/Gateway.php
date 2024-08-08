<?php

namespace App\Gateways\Sms;

abstract class Gateway
{


    abstract public function send(string $to, string $message);
}
