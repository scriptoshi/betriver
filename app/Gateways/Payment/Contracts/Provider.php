<?php

namespace App\Gateways\Payment\Contracts;

use App\Models\Deposit;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

interface Provider
{

    /**
     * get the name (ID) of the gateway
     */

    public function getName();
    /**
     * Handle deposit request.
     *
     * @param  Deposit $deposit
     */
    public function deposit(Deposit $deposit);

    /**
     * Handle IPN Webhook from provider.
     *
     * @param  Request $request
     */
    public function webhook(Request $request, string $type = 'deposit');

    /**
     * Handle IPN Webhook from provider.
     *
     * @param  Collection $withdraws
     */
    public function withdraw(Collection $withdraws);

    /**
     * Get the config values for the gateway
     * 
     * @return \Illuminate\Support\Collection
     */
    public function getConfig(): Collection;

    /**
     * save the config values for the gateway
     *
     * @param  \Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Support\Collection
     */
    public function setConfig(Request $request): Collection;
}
