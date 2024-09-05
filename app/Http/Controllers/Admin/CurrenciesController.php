<?php

namespace App\Http\Controllers;

use App\Enums\WithdrawGateway;
use App\Http\Controllers\Controller;
use App\Http\Resources\Currency as CurrencyResource;
use App\Models\Currency;
use App\Support\Rate;
use Inertia\Inertia;

use Illuminate\Http\Request;

class CurrenciesController extends Controller
{

    /**
     * update currencies for a gateway.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function load(Request $request, WithdrawGateway $gateway)
    {
        $gateway->driver()->updateCurrencies();
        Rate::update();
        return back();
    }
}
