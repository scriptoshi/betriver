<?php

namespace App\Gateways\Payment;

use Closure;
use App\Gateways\Payment\Contracts\Factory;
use App\Gateways\Payment\Drivers\CoinPayments;
use App\Gateways\Payment\Drivers\NowPayments;
use App\Gateways\Payment\Drivers\Payeer;
use App\Gateways\Payment\Drivers\Paypal;
use Illuminate\Support\Manager;
use InvalidArgumentException;

class PaymentManager extends Manager implements Factory
{

    /**
     * The array of registered "drivers".
     *
     * @var array
     */
    protected $registered = [
        "nowpayments",
        "paypal",
        "coinpayments",
        "payeer"
    ];

    /**
     * get all registred gateways
     *
     * @param  string  $driver
     * @return mixed
     */
    public function gateways()
    {
        return $this->registered;
    }

    /**
     * register a driver name.
     *
     * @param  string  $driver
     * @return mixed
     */
    public function register($driver)
    {
        return $this->registered[] = $driver;
    }

    /**
     * unregister a driver name.
     *
     * @param  string  $driver
     * @return mixed
     */
    public function unRegister($driver)
    {
        unset($this->registered[$driver]);
    }


    /**
     * Get a driver instance.
     *
     * @param  string  $driver
     * @return mixed
     */
    public function with($driver)
    {
        return $this->driver($driver);
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \App\Gateways\Payment\Drivers\CoinPayments;
     */
    protected function createCoinpaymentsDriver(): CoinPayments
    {
        $public_key = config('services.coinpayments.public_key', settings('coinpayments.public_key'));
        $private_key = config('services.coinpayments.private_key',    settings('coinpayments.private_key'));
        $merchant_id =   config('services.coinpayments.merchant_id',   settings('coinpayments.merchant_id'));
        $ipn_secret =  config('services.coinpayments.ipn_secret',   settings('coinpayments.ipn_secret'));
        return new CoinPayments(public_key: $public_key, private_key: $private_key, merchant_id: $merchant_id, ipn_secret: $ipn_secret);
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \App\Gateways\Payment\Drivers\NowPayments;
     */
    protected function createNowpaymentsDriver(): NowPayments
    {
        $api_key = config('services.nowpayments.api_key', settings('nowpayments.api_key'));
        $api_secret = config('services.nowpayments.api_secret', settings('nowpayments.api_secret'));
        return new NowPayments(api_key: $api_key, api_secret: $api_secret);
    }
    /**
     * Create an instance of the specified driver.
     *
     * @return \App\Gateways\Payment\Drivers\Paypal;
     */
    protected function createPaypalDriver(): Paypal
    {

        $client_id = config('services.paypal.client_id', settings('paypal.client_id'));
        $client_secret = config('services.paypal.client_secret', settings('paypal.client_id'));
        return new Paypal(client_secret: $client_secret, client_id: $client_id);
    }
    /**
     * Create an instance of the specified driver.
     *
     * @return \App\Gateways\Payment\Drivers\Payeer;
     */
    protected function createPayeerDriver(): Payeer
    {
        $shop = config('services.payeer.shop', settings('payeer.shop'));
        $merchant_key = config('services.payeer.merchant_key', settings('payeer.merchant_key'));
        $accountNumber = config('services.payeer.accountNumber', settings('payeer.accountNumber'));
        $apiId = config('services.payeer.apiId', settings('payeer.apiId'));
        $apiSecret = config('services.payeer.apiSecret', settings('payeer.apiSecret'));
        return new Payeer(
            shop: $shop,
            merchant_key: $merchant_key,
            accountNumber: $accountNumber,
            apiId: $apiId,
            apiSecret: $apiSecret
        );
    }



    /**
     * Forget all of the resolved driver instances.
     *
     * @return $this
     */
    public function forgetDrivers()
    {
        $this->drivers = [];

        return $this;
    }

    /**
     * Set the container instance used by the manager.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @return $this
     */
    public function setContainer($container)
    {
        $this->container = $container;
        $this->config = $container->make('config');
        return $this;
    }

    /**
     * Get the default driver name.
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function getDefaultDriver()
    {
        throw new InvalidArgumentException('No Payment driver was specified.');
    }

    /**
     * Register a custom driver creator Closure.
     *
     * @param  string  $driver
     * @param  \Closure  $callback
     * @return $this
     */
    public function extend($driver, Closure $callback)
    {
        $this->customCreators[$driver] = $callback;
        $this->register($driver);
        return $this;
    }
}
