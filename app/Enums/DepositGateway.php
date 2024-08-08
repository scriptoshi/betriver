<?php

namespace App\Enums;

use App\Gateways\Payment\Facades\Payment;

enum DepositGateway: string
{
    case COINPAYMENTS = 'coinpayments';
    case NOWPAYMENTS = 'nowpayments';
    case PAYEER = 'payeer';
    case PAYPAL = 'paypal';

    public function type()
    {
        return match ($this) {
            static::COINPAYMENTS,
            static::NOWPAYMENTS => 'crypto',
            static::PAYEER,
            static::PAYPAL => 'fiat',
            default => 'fiat'
        };
    }

    public function driver()
    {
        return Payment::driver($this->value);
    }
}
