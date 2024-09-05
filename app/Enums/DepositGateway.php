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

    public function info()
    {
        return [
            'logo' => settings("{$this->value}.logo"),
            'name' => settings("{$this->value}.name"),
        ];
    }


    /**
     * Get an array of  names.
     *
     * @return array<string, array>
     */
    public static function getNames(): array
    {

        return array_reduce(self::cases(), function ($carry, $case) {
            $carry[$case->value] = [
                'value' => $case->value,
                'subtitle' => null,
                'label' => settings("{$case->value}.name"),
                'img' => settings("{$case->value}.logo"),
                'enable_deposit' => settings("{$case->value}.enable_deposit"),
            ];
            return $carry;
        }, []);
    }

    public function getSubtitle(): string
    {
        return match ($this) {
            static::COINPAYMENTS => 'Cryptocurrency Payment Gateway',
            static::NOWPAYMENTS => 'Instant Crypto Payments',
            static::PAYEER => 'Global E-wallet & Online Payment System',
            static::PAYPAL => 'Secure Online Payments',
        };
    }
}
