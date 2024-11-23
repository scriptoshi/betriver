<?php

namespace App\Gateways\Payment\Actions;

use App\Enums\TransactionAction;
use App\Enums\TransactionType;
use App\Models\Deposit;
use App\Models\Transaction;
use App\Notifications\BalanceDeposit;

class DepositTx
{
    public function create(Deposit $deposit): Transaction
    {
        $user = $deposit->user;
        $balance_before = $user->balance;
        $fees_percent = $user->level->settings('deposit_fees'); // eg 2 (is percentage)
        $fees = ($deposit->amount * $fees_percent) / 100;
        $netAmount = $deposit->amount - $fees;
        $user->increment('balance', $deposit->amount);
        $tx =  $deposit->transaction()->create([
            'user_id' => $deposit->user_id,
            'description' => 'Deposit via Coinpayments',
            'amount' => $netAmount,
            'fees' => $fees,
            'balance_before' => $balance_before,
            'action' => TransactionAction::CREDIT,
            'type' => TransactionType::DEPOSIT
        ]);
        $user->notify(new BalanceDeposit($tx));
        return $tx;
    }

    public function fees() {}
}
