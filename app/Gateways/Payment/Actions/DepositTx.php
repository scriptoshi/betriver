<?php

namespace App\Gateways\Payment\Actions;

use App\Enums\TransactionAction;
use App\Enums\TransactionType;
use App\Models\Deposit;
use App\Models\Transaction;

class DepositTx
{
    public function create(Deposit $deposit): Transaction
    {
        $user = $deposit->user;
        $balance_before = $user->balance;
        $user->increment('balance', $deposit->amount);
        return $deposit->transaction()->create([
            'user_id' => $deposit->user_id,
            'description' => 'Deposit via Coinpayments',
            'amount' => $deposit->amount,
            'balance_before' => $balance_before,
            'action' => TransactionAction::CREDIT,
            'type' => TransactionType::DEPOSIT
        ]);
    }
}
