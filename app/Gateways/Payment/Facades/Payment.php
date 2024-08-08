<?php

namespace App\Gateways\Payment\Facades;

use App\Gateways\Payment\Contracts\Factory;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Gateways\Payment\Contracts\Provider driver(string $driver = null)
 * @method static array gateways()
 * @method static void register()
 * @method static void unRegister()
 *
 * @see App\Gateways\Payment\PaymentManager
 */
class Payment extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Factory::class;
    }
}
