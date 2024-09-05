<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DepositStatus;
use App\Enums\StakeStatus;
use App\Enums\TransactionAction;
use App\Enums\TransactionGateway;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Enums\WithdrawStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\Transaction as TransactionResource;
use App\Models\Transaction;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TransactionsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 25;
        $query  = Transaction::query()->with(['user']);
        if (!empty($keyword)) {
            $query->orWhereHas('user', function (Builder $query) use ($keyword) {
                $query->where('email', 'LIKE', "%$keyword%")
                    ->orWhere('name', 'LIKE', "%$keyword%");
            })
                ->orWhere('uuid', 'LIKE', "%$keyword%")
                ->orWhere('description', 'LIKE', "%$keyword%")
                ->orWhere('amount', 'LIKE', "%$keyword%")
                ->orWhere('type', 'LIKE', "%$keyword%");
        }
        $transactionsItems = $query->latest()->paginate($perPage);
        $transactions = TransactionResource::collection($transactionsItems);
        return Inertia::render('Admin/Transactions/Index', compact('transactions'));
    }


    /**
     * Revert a transaction and update related models.
     *
     * This function reverses the effects of a given transaction by creating an opposite
     * transaction and updating the user's balance. It also handles specific logic based
     * on the transaction type.
     *
     * @param Transaction $transaction the transaction to revert
     * @return bool Returns true if the reversion was successful, false otherwise
     * @throws ModelNotFoundException If the transaction is not found
     * @throws \Exception If there's an error during the reversion process
     */
    function reverse(Request $request, Transaction $transaction)
    {
        DB::beginTransaction();

        try {

            $user = $transaction->user;
            // Create a new opposite transaction
            $revertedTransaction = new Transaction();
            $revertedTransaction->user_id = $user->id;
            $revertedTransaction->transactable_type = $transaction->transactable_type;
            $revertedTransaction->transactable_id = $transaction->transactable_id;
            $revertedTransaction->description = "Revert: " . $transaction->description;
            $revertedTransaction->amount = $transaction->amount;
            $revertedTransaction->balance_before = $user->balance;
            $revertedTransaction->action = $transaction->action === TransactionAction::CREDIT
                ? TransactionAction::DEBIT
                : TransactionAction::CREDIT;
            $revertedTransaction->type = TransactionType::REVERSED;

            // Update user balance
            if ($revertedTransaction->action === TransactionAction::CREDIT) {
                $user->balance += $revertedTransaction->amount;
            } else {
                $user->balance -= $revertedTransaction->amount;
            }

            // Handle specific logic based on transaction type
            switch ($transaction->type) {
                case TransactionType::DEPOSIT:
                    // Update deposit status or handle deposit-specific logic
                    $deposit = $transaction->transactable;
                    $deposit->status = DepositStatus::REVERSED;
                    $deposit->save();
                    break;
                case TransactionType::WITHDRAW:
                    // Update withdraw status or handle withdraw-specific logic
                    $withdraw = $transaction->transactable;
                    $withdraw->status = WithdrawStatus::REVERSED;
                    $withdraw->save();
                    break;
                case TransactionType::BET:
                    // Handle bet-specific logic, e.g., updating stake status
                    $stake = $transaction->transactable;
                    $stake->status = 'cancelled';
                    $stake->save();
                    break;
                    // I should Add more cases for other transaction types as needed
            }
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Request $request, Transaction $transaction)
    {
        $transaction->delete();
        return back()->with('success', 'Transaction deleted!');
    }
}
