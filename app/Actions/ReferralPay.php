<?php

namespace App\Actions;

use App\Enums\CommissionType;
use App\Enums\TransactionAction;
use App\Enums\TransactionType;
use App\Models\User;
use App\Models\Commission;
use App\Models\Payout;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ReferralPay
{
    /**
     * Create commission payments for referrers based on a transaction
     *
     * @param CommissionType $type The type of commission (deposit, slip, ticket)
     * @param User $user The user who triggered the commission
     * @param float $amount The amount to calculate commission from
     * @return void
     */
    public static function create(CommissionType $type, User $user, $amount)
    {
        // Get user's upline referral chain
        $upline = $user->referral;
        if (!$upline) return;

        // Split the referral chain into individual refIds and reverse to get correct levels
        // Last refId in string becomes first (level 1)
        $refIds = Str::of($upline)->explode(':')->reverse()->values()->toArray();

        // Get commission rates for this type
        $commissions = Commission::where('type', $type->value)
            ->where('active', true)
            ->orderBy('level')
            ->get();

        // Process each level of the referral chain
        foreach ($refIds as $index => $refId) {
            // Get commission for this level (index + 1 since levels start at 1)
            $commission = $commissions->where('level', $index + 1)->first();

            // Skip if no commission defined for this level
            if (!$commission) continue;

            // Find the referring user
            $referrer = User::where('refId', $refId)->first();
            if (!$referrer) continue;

            // Calculate commission amount
            $commissionAmount = ($amount * $commission->percent) / 100;

            // Create the commission transaction and payout
            DB::transaction(function () use ($type, $referrer, $commissionAmount, $user, $commission) {
                // Create payout record
                $payout = Payout::create([
                    'commission_id' => $commission->id,
                    'user_id' => $referrer->id,
                    'referral_id' => $user->id,
                    'uuid' => Str::uuid(),
                    'description' => "Level {$commission->level} {$type->value} commission from {$user->name}",
                    'amount' => $commissionAmount,
                    'percent' => $commission->percent,
                ]);

                // Create transaction record with polymorphic relationship to payout
                $transaction = new Transaction([
                    'user_id' => $referrer->id,
                    'uuid' => Str::uuid(),
                    'description' => "Level {$commission->level} {$type->value} commission from {$user->name}",
                    'amount' => $commissionAmount,
                    'balance_before' => $referrer->balance,
                    'action' =>  TransactionAction::CREDIT,
                    'type' =>  TransactionType::PAYOUT
                ]);
                // Associate transaction with payout
                $payout->transaction()->save($transaction);
                // Update referrer's balance
                $referrer->increment('balance', $commissionAmount);
            });
        }
    }
}
