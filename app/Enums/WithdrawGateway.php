<?php

namespace App\Enums;

use App\Gateways\Payment\Facades\Payment;
use App\Support\Rate;

enum WithdrawGateway: string
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

    public function settings($setting)
    {
        return settings("{$this->value}.$setting");
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
                'destination' => $case->destinationDetails(),
                'subtitle' => null,
                'label' => settings("{$case->value}.name"),
                'img' => settings("{$case->value}.logo"),
                'enable_withdraw' => settings("{$case->value}.enable_withdraw"),
                'max' => settings("{$case->value}.max_withdraw_limit"),
                'min' => settings("{$case->value}.min_withdraw_limit"),
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

    public function destinationDetails(): string
    {
        return match ($this) {
            static::COINPAYMENTS => __('Destination {symbol} address'),
            static::NOWPAYMENTS => __('Destination {symbol} address'),
            static::PAYEER => 'Destination Payeer Account Number',
            static::PAYPAL => 'Destination PayPal Email address',
        };
    }
}
