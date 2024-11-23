<?php

namespace App\Http\Controllers;

use App\Enums\DepositGateway;
use App\Enums\DepositStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\Deposit as DepositResource;
use App\Models\Currency;
use App\Models\Deposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class DepositsController extends Controller
{


    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $user = $request->user();
        $config = $user->level->config();
        $keyword = $request->get('search');
        $perPage = 10;
        $query  = Deposit::query()->where('user_id', $request->user()->id);
        if (!empty($keyword)) {
            $query->where('uuid', 'LIKE', "%$keyword%")
                ->orWhere('gateway', 'LIKE', "%$keyword%")
                ->orWhere('amount', 'LIKE', "%$keyword%")
                ->orWhere('data', 'LIKE', "%$keyword%");
        }
        $depositsItems = $query->latest()->paginate($perPage);
        $deposits = DepositResource::collection($depositsItems);
        return Inertia::render('Deposits/Create', [
            'gateways' => DepositGateway::getNames(),
            'currencies' => Currency::active()->get()->groupBy('gateway'),
            'maxDaily' =>  $config['max_daily_deposit'],
            'maxMonthly' =>  $config['max_monthly_deposit'],
            'totalToday' => Deposit::query()
                ->whereIn('status', DepositStatus::quotaStatus())
                ->where('created_at', '>=', now()->startOfDay())
                ->sum('amount'),
            'totalThisMonth' => Deposit::query()
                ->whereIn('status', DepositStatus::quotaStatus())
                ->where('created_at', '>=', now()->startOfMonth())
                ->sum('amount'),
            'deposits' => $deposits

        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'numeric|required',
            'currency' => 'integer|exists:currencies,id|required',
            'gateway' => ['string', 'required', new Enum(DepositGateway::class)],
        ]);
        $gateway = DepositGateway::from($request->gateway);
        $user = $request->user();
        $config = $user->level->config();
        $maxDaily =  $config['max_daily_deposit'];
        $maxMonthly =   $config['max_monthly_deposit'];
        $fees =   (float) $config['deposit_fees'];
        $totalToday = Deposit::query()
            ->whereIn('status', DepositStatus::quotaStatus())
            ->where('created_at', '>=', now()->startOfDay())
            ->sum('amount');
        $totalThisMonth = Deposit::query()
            ->whereIn('status', DepositStatus::quotaStatus())
            ->where('created_at', '>=', now()->startOfMonth())
            ->sum('amount');
        if (($totalThisMonth + $request->amount) > $maxMonthly) {
            throw ValidationException::withMessages(['amount' => [__('Deposit Exceeds your monthly quota')]]);
        }

        if (($totalToday + $request->amount) > $maxDaily) {
            throw ValidationException::withMessages(['amount' => [__('Deposit Exceeds your daily quota')]]);
        }
        $currency = Currency::findOrFail($request->currency);
        $deposit = new Deposit;
        $deposit->user_id = $request->user()->id;
        $deposit->gateway = $gateway;
        $deposit->remoteId = null;
        $deposit->from = null;
        $deposit->gross_amount = $fees > 0
            ? static::calculateGrossAmount((float)$request->amount, $fees)
            : $request->amount;
        $deposit->fees = $fees;
        $deposit->amount = $request->amount;
        $deposit->amount_currency = settings('site.currency_code');
        $deposit->gateway_currency = $currency->code;
        // Estimate!!, will change
        $deposit->gateway_amount = bcdiv($deposit->gross_amount, $currency->rate, $currency->precision ?? 2);
        $deposit->data = null;
        $deposit->status = DepositStatus::PENDING;
        $deposit->save();
        return $gateway->driver()->deposit($deposit);
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function cancel(Request $request, Deposit $deposit)
    {
        Gate::authorize('update', $deposit);
        $deposit->status = DepositStatus::FAILED;
        $deposit->gateway_error = __('Transaction cancelled at the payment gateway');
        $deposit->save();
        return  redirect()->route('deposits.show', $deposit);
    }

    /**
     * Handle return after deposit
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function gatewayReturn(Request $request, Deposit $deposit)
    {
        Gate::authorize('update', $deposit);
        return $deposit->gateway
            ->driver()
            ->returned($request, $deposit);
    }

    /**
     * Handle in notifications
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function webhooks(Request $request, $gateway,  Deposit $deposit)
    {
        $gateway = DepositGateway::tryFrom($gateway);
        Log::info('IPN', $request->all());
        return $gateway->driver()->webhook($request, 'deposit');
    }


    /**
     * manually check status via polling
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function checkStatus(Request $request,  Deposit $deposit)
    {
        $deposit->gateway->driver()->checkDepositStatus($deposit);
        return back();
    }




    protected static function calculateGrossAmount($netAmount, $feePercentage)
    {
        // Ensure the fee percentage is expressed as a decimal
        $feeRate = $feePercentage / 100;
        // Calculate the gross amount
        // Formula: netAmount = grossAmount - (grossAmount * feeRate)
        // Simplified: netAmount = grossAmount * (1 - feeRate)
        // Therefore: grossAmount = netAmount / (1 - feeRate)
        $grossAmount = $netAmount / (1 - $feeRate);
        // Round to 2 decimal places for currency
        return round($grossAmount, 2);
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request, Deposit $deposit)
    {
        $deposit->load(['user', 'transaction']);
        return Inertia::render('Deposits/Show', [
            'deposit' => new DepositResource($deposit)
        ]);
    }
}
