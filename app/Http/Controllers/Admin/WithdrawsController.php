<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TransactionAction;
use App\Enums\TransactionType;
use App\Enums\WithdrawGateway;
use App\Enums\WithdrawStatus;
use App\Gateways\Payment\Drivers\NowPayments;
use App\Gateways\Payment\Facades\Payment;
use App\Http\Controllers\Controller;
use App\Http\Resources\Withdraw as WithdrawResource;
use App\Models\Transaction;
use App\Models\Withdraw;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;

class WithdrawsController extends Controller
{


    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $status = $request->get('status');
        $items = (int) $request->get('items');
        $items = in_array($items, [5, 10, 25, 50, 100, 500, 1000]) ? $items : 25;
        $query  = Withdraw::with(['user']);
        if (!empty($keyword)) {
            $query->where('uuid', 'LIKE', "%$keyword%")
                ->orWhere('gateway', 'LIKE', "%$keyword%")
                ->orWhere('amount', 'LIKE', "%$keyword%")
                ->orWhere('data', 'LIKE', "%$keyword%");
        }
        if (!empty($status)) {
            $query->where('status', $status);
        }
        $withdrawsItems = $query->latest()->paginate($items);
        $withdraws = WithdrawResource::collection($withdrawsItems);

        return Inertia::render('Admin/Withdraws/Index', [
            'statuses' => WithdrawStatus::getNames(),
            'withdraws' => $withdraws,
            'nowpayments' => Withdraw::where('gateway', WithdrawGateway::NOWPAYMENTS)->pluck('id'),
        ]);
    }



    /**
     * force fail a withdraw.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function fail(Request $request, Withdraw $withdraw)
    {
        $withdraw->status = WithdrawStatus::FAILED;
        $withdraw->gateway_error = __('Withdraw cancelled by admin');
        $withdraw->save();
        return  back();
    }


    /**
     * batch process withdrawals
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function batch(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'username' => 'nullable|string',
            'password' => 'nullable|string',
        ]);
        $responses = Withdraw::query()->findMany($request->id)
            ->groupBy('gateway')
            ->mapWithKeys(function (Collection $withdrawals, WithdrawGateway $gateway) use ($request) {
                $driver = $gateway->driver();
                try {
                    if ($gateway == WithdrawGateway::NOWPAYMENTS)
                        $driver->login($request->username, $request->password);
                    $driver->withdraw($withdrawals);
                    return null;
                } catch (\Exception $e) {
                    return $gateway->value . ": Error Encountered - " . $e->getMessage();
                }
            });
        if ($responses->filter()->count()) return back()->with('error', $responses->implode("\n"));
        return  back();
    }

    /**
     * update withdraw status
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function status(Request $request, Withdraw $withdraw)
    {
        $status = WithdrawStatus::from($request->status);
        if ($status ===  WithdrawStatus::REVERSED) {
            $this->reverse($withdraw);
            return back();
        }
        $withdraw->status =  $status;
        $withdraw->save();
        return  back();
    }


    /**
     * refresh status at gateway
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function refresh(Request $request, Withdraw $withdraw)
    {
        $withdraw->gateway->driver()->updateWithdrawStatus($withdraw);
        return  back();
    }

    /**
     * submit two factor auth for nowpayments;
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function twofa(Request $request, Withdraw $withdraw)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
            'code' => 'required'
        ]);
        if ($withdraw->gateway === WithdrawGateway::NOWPAYMENTS) {
            $nowpayments =  Payment::driver('nowpayments');
            $nowpayments->login($request->username, $request->password);
            $nowpayments->verifyPayout($withdraw->batchId, $request->code);
            $nowpayments->updateWithdrawStatus($withdraw);
        }
        return  back();
    }

    /**
     * Revert a transaction and update related models.
     *
     * This function reverses the effects of a given transaction by creating an opposite
     * transaction and updating the user's balance. It also handles specific logic based
     * on the transaction type.
     *
     * @param Withdraw $withdraw the withdraw to revert
     * @return bool Returns true if the reversion was successful, false otherwise
     * @throws \Exception If there's an error during the reversion process
     */
    protected function reverse(Withdraw $withdraw)
    {
        DB::beginTransaction();

        try {
            $withdraw->status = WithdrawStatus::REVERSED;
            $withdraw->save();
            $transaction = $withdraw->transaction;
            $user = $transaction->user;
            // Create a new opposite transaction
            $revertedTransaction = new Transaction();
            $revertedTransaction->user_id = $user->id;
            $revertedTransaction->transactable_type = $transaction->transactable_type;
            $revertedTransaction->transactable_id = $transaction->transactable_id;
            $revertedTransaction->description = "Revert: " . $transaction->description;
            $revertedTransaction->amount = $transaction->amount;
            $revertedTransaction->balance_before = $user->balance;
            $revertedTransaction->action =  TransactionAction::CREDIT;
            $revertedTransaction->type = TransactionType::REVERSED;
            // Update user balance
            $user->balance += $revertedTransaction->amount;
            $user->save();
            $revertedTransaction->save();
            DB::commit();
            return back();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', "Error reverting transaction: " . $e->getMessage());
        }
    }

    /**
     * force complete/ approve a withdraw.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function complete(Request $request, Withdraw $withdraw)
    {
        if ($withdraw->status == WithdrawStatus::COMPLETE) {
            return back()->with('error', "Withdraw already complete");
        }
        $withdraw->status = WithdrawStatus::COMPLETE;
        $withdraw->gateway_error = null;
        $withdraw->save();

        return  back();
    }
}
