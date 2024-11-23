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
     * get the name (ID) of the gateway
     */

    public function updateCurrencies();
    /**
     * Handle deposit request.
     *
     * @param  Deposit $deposit
     */
    public function deposit(Deposit $deposit);

    /**
     * Manually check payment/deposit status from CoinPayments API
     * 
     * @param string $txnId The transaction ID to check
     * @return object|null Payment status data or null on failure
     * @throws \Exception If API request fails
     */
    public function checkDepositStatus(Deposit $deposit);
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

    /**
     * Handle return after payment
     *
     * @param  \Illuminate\Http\Request $request
     * 
     * @return \Illuminate\Support\Collection
     */
    public function returned(Request $request, Deposit $deposit);


    /**
     * Update the status of a withdraw (payout) at provider.
     *
     * @param Withdraw $withdraw the withdraw in our system
     * @return bool Returns true if the update was successful, false otherwise
     */
    public function updateWithdrawStatus(Withdraw $withdraw): bool;
}
