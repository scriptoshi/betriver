<?php

namespace App\Http\Controllers;

use App\Enums\TransactionAction;
use App\Enums\TransactionType;
use App\Enums\WithdrawGateway;
use App\Enums\WithdrawStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\Whitelist as ResourcesWhitelist;
use App\Http\Resources\Withdraw as WithdrawResource;
use App\Models\Currency;
use App\Models\Transaction;
use App\Models\Whitelist;
use App\Models\Withdraw;
use App\Support\Rate;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class WithdrawsController extends Controller
{


    /**
     * Show the form for creating a new withdraw.
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 10;
        $query  = Withdraw::query()
            ->where('user_id', $request->user()->id);
        if (!empty($keyword)) {
            $query->where('uuid', 'LIKE', "%$keyword%")
                ->orWhere('gateway', 'LIKE', "%$keyword%")
                ->orWhere('amount', 'LIKE', "%$keyword%")
                ->orWhere('data', 'LIKE', "%$keyword%");
        }

        $withdrawsItems = $query->latest()->paginate($perPage);
        $withdraws = WithdrawResource::collection($withdrawsItems);
        $accounts = $request->user()
            ->whitelists()
            ->with(['currency'])
            ->approved()
            ->get();
        return Inertia::render('Withdraws/Create', [
            'accounts' => ResourcesWhitelist::collection($accounts),
            'gateways' => WithdrawGateway::getNames(),
            'withdraws' => $withdraws,
            'currencies' => Currency::active()->get()->groupBy('gateway'),
        ]);
    }

    /**
     * Store a newly created withdrawal in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $request->validate([
            'tos' => 'accepted',
            'amount' => 'numeric|required',
            'account_id' => 'integer|exists:whitelists,id|required'
        ]);
        $account = Whitelist::with([])->find($request->account_id);
        $gateway = WithdrawGateway::from($account->currency->gateway);
        $user = $request->user();
        $config = $user->level->config();
        $fees =   (float) $config['withdraw_fees'];
        $currencyCode = settings('site.currency_code');
        // check limits
        $max_withdraw_limit = $gateway->settings('max_withdraw_limit');
        $min_withdraw_limit = $gateway->settings('min_withdraw_limit');
        if ($max_withdraw_limit > 0 && $request->amount > $max_withdraw_limit) {
            throw ValidationException::withMessages(['amount' => [__('Withdraw Exceeds gateway limit of :limit :currency', ['limit' => $max_withdraw_limit, 'currency' => $currencyCode])]]);
        }
        if ($min_withdraw_limit > 0 && $request->amount < $min_withdraw_limit) {
            throw ValidationException::withMessages(['amount' => [__('Withdraw is less than gateway minimum of :limit :currency', ['limit' => $min_withdraw_limit, 'currency' => $currencyCode])]]);
        }
        if ($user->balance < $request->amount) {
            throw ValidationException::withMessages(['amount' => [__('Low balance')]]);
        }
        $net_amount = $fees > 0
            ? static::calculateNetAmount((float)$request->amount, $fees)
            : $request->amount;
        $currency =  $account->currency;
        $withdraw = new Withdraw;
        $withdraw->user_id = $request->user()->id;
        $withdraw->to =  $account->payout_address;
        $withdraw->gateway = $gateway;
        $withdraw->remoteId = null;
        $withdraw->gross_amount = $net_amount; // amount the user actually gets after fees
        // estimate, could change
        $withdraw->gateway_amount = bcdiv($net_amount, $currency->rate, $currency->precision ?? 2);
        $withdraw->fees = $request->amount - $net_amount;
        $withdraw->amount = $request->amount;
        $withdraw->currency = settings('site.currency_code');
        $withdraw->gateway_currency = $currency->code;
        $withdraw->data = null;
        $withdraw->status = WithdrawStatus::PENDING;
        DB::beginTransaction();
        try {
            // Update withdraw status
            $withdraw->status = WithdrawStatus::REVIEW;
            $withdraw->save();
            // Create a new transaction
            $transaction = new Transaction([
                'user_id' => $user->id,
                'amount' => $withdraw->amount,
                'fees' => $withdraw->fees,
                'balance_before' => $user->balance,
                'action' => TransactionAction::DEBIT,
                'type' => TransactionType::WITHDRAW,
                'description' => 'Withdrawal request: ' . $withdraw->uuid,
            ]);
            $withdraw->transaction()->save($transaction);
            // Deduct the amount from user's balance
            $user->decrement('balance', $withdraw->amount);
            DB::commit();
            return redirect()->route('withdraws.show', ['withdraw' => $withdraw])->with('success', __('Withdrawal confirmed and sent for review.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', __('Error confirming withdrawal: :error', ['error' => $e->getMessage()]));
        }
        return redirect()->route('withdraws.show',  $withdraw);
    }



    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function cancel(Request $request, Withdraw $withdraw)
    {
        Gate::authorize('update', $withdraw);

        if ($withdraw->status != WithdrawStatus::PENDING && $withdraw->status != WithdrawStatus::REVIEW) {
            return back()->with('error', __('Withdraw already processing. Cannot cancel!'));
        }
        DB::beginTransaction();
        try {
            // Update withdraw status
            $withdraw->status = WithdrawStatus::CANCELLED;
            $withdraw->save();
            // Find and update the associated transaction
            $transaction = $withdraw->transaction;
            if ($transaction) {
                // Reverse the transaction
                $user = $withdraw->user;
                $user->increment('balance', $withdraw->amount);
                $transaction->update([
                    'description' => 'Withdrawal cancelled: ' . $transaction->description,
                    'action' => TransactionAction::CREDIT,
                    'type' => TransactionType::WITHDRAW_CANCELLED,
                    'amount' => $withdraw->amount,
                    'balance_before' => $user->balance - $withdraw->amount,
                ]);
            }

            DB::commit();
            return redirect()->route('withdraws.show', $withdraw)->with('success', __('Withdrawal cancelled successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', __('Error cancelling withdrawal: :error', ['error' => $e->getMessage()]));
        }
    }


    function calculateNetAmount($grossAmount, $feePercentage)
    {
        $feeDecimal = (100 - $feePercentage) / 100;
        return $grossAmount * $feeDecimal;
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request, Withdraw $withdraw)
    {
        $withdraw->load(['transaction']);
        return Inertia::render('Withdraws/Show', [
            'withdraw' => new WithdrawResource($withdraw)
        ]);
    }
}
