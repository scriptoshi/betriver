<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Deposit;
use App\Models\Withdraw;
use App\Enums\TransactionType;
use App\Enums\TransactionAction;
use App\Enums\DepositStatus;
use App\Enums\WithdrawStatus;
use Faker\Factory as Faker;

class FakeTransactionsSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $user = User::findOrFail(1);

        // Generate fake deposits
        for ($i = 0; $i < 10; $i++) {
            $amount = $faker->randomFloat(2, 10, 1000);
            $deposit = Deposit::create([
                'user_id' => $user->id,
                'uuid' => $faker->uuid,
                'gateway' => $faker->randomElement(['coinpayments', 'nowpayments', 'payeer', 'paypal']),
                'remoteId' => $faker->md5,
                'gross_amount' => $amount,
                'fees' => $amount * 0.02, // 2% fee
                'amount' => $amount * 0.98,
                'amount_currency' => 'USD',
                'gateway_amount' => $amount,
                'gateway_currency' => 'USD',
                'status' => DepositStatus::COMPLETE,
            ]);

            Transaction::create([
                'user_id' => $user->id,
                'transactable_type' => $deposit->getMorphClass(),
                'transactable_id' => $deposit->id,
                'uuid' => $faker->uuid,
                'description' => 'Deposit via ' . $deposit->gateway->value,
                'amount' => $deposit->amount,
                'balance_before' => $user->balance ?? 0,
                'action' => TransactionAction::CREDIT,
                'type' => TransactionType::DEPOSIT,
            ]);

            $user->balance += $deposit->amount;
            $user->save();
        }

        // Generate fake withdraws
        for ($i = 0; $i < 15; $i++) {
            $amount = $faker->randomFloat(2, 10, 500);
            $withdraw = Withdraw::create([
                'user_id' => $user->id,
                'uuid' => $faker->uuid,
                'gateway' => $faker->randomElement(['coinpayments', 'nowpayments', 'payeer', 'paypal']),
                'remoteId' => $faker->md5,
                'gross_amount' => $amount,
                'fees' => $amount * 0.03, // 3% fee
                'amount' => $amount * 0.97,
                'currency' => 'USD',
                'gateway_currency' => 'USD',
                'status' => WithdrawStatus::COMPLETE,
            ]);

            Transaction::create([
                'user_id' => $user->id,
                'transactable_type' => $withdraw->getMorphClass(),
                'transactable_id' => $withdraw->id,
                'uuid' => $faker->uuid,
                'description' => 'Withdraw via ' . $withdraw->gateway->value,
                'amount' => $withdraw->amount,
                'balance_before' =>  $user->balance ?? 0,
                'action' => TransactionAction::DEBIT,
                'type' => TransactionType::WITHDRAW,
            ]);

            $user->balance -= $withdraw->amount;
            $user->save();
        }

        // Generate other fake transactions
        $transactionTypes = [
            TransactionType::TRANSFER,
            TransactionType::WIN,
            TransactionType::BET,
            TransactionType::BET_REFUND,
            TransactionType::PAYOUT,
            TransactionType::TRADE_OUT_LIABILITY,
            TransactionType::TRADE_OUT_PROFIT,
        ];

        /*for ($i = 0; $i < 20; $i++) {
            $type = $faker->randomElement($transactionTypes);
            $amount = $faker->randomFloat(2, 1, 100);
            $action = $faker->randomElement([TransactionAction::CREDIT, TransactionAction::DEBIT]);

            Transaction::create([
                'user_id' => $user->id,
                'transactable_type' => null,
                'transactable_id' => null,
                'uuid' => $faker->uuid,
                'description' => $type->name() . ' transaction',
                'amount' => $amount,
                'balance_before' => $user->balance,
                'action' => $action,
                'type' => $type,
            ]);

            if ($action === TransactionAction::CREDIT) {
                $user->balance += $amount;
            } else {
                $user->balance -= $amount;
            }
            $user->save();
        }*/
    }
}
