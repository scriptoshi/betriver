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
        $config = settings()->for('coinpayments')->only([
            'public_key',
            'private_key',
            'merchant_id',
            'ipn_secret',
        ]);
        return new CoinPayments(...$config);
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \App\Gateways\Payment\Drivers\NowPayments;
     */
    protected function createNowpaymentsDriver(): NowPayments
    {
        $config = settings()->for('nowpayments')->only(['api_key', 'api_secret']);
        return new NowPayments(...$config);
    }
    /**
     * Create an instance of the specified driver.
     *
     * @return \App\Gateways\Payment\Drivers\Paypal;
     */
    protected function createPaypalDriver(): Paypal
    {
        $config = settings()->for('paypal')->only([
            'client_id', 'client_secret'
        ]);
        return new Paypal(...$config);
    }
    /**
     * Create an instance of the specified driver.
     *
     * @return \App\Gateways\Payment\Drivers\Payeer;
     */
    protected function createPayeerDriver(): Payeer
    {

        $config = settings()->for('payeer')->only([
            'shop',
            'merchant_key',
            'accountNumber',
            'apiId',
            'apiSecret',
        ]);
        return new Payeer(...$config);
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
