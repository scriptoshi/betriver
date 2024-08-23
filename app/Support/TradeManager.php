<?php

namespace App\Support;

use App\Models\Stake;
use App\Models\Trade;
use App\Enums\StakeType;
use App\Enums\StakeStatus;
use App\Enums\TradeStatus;
use App\Enums\TransactionAction;
use App\Enums\TransactionType;
use DB;
use Illuminate\Database\Eloquent\Builder;

class TradeManager
{

    /**
     * Converts decimal odds to American odds.
     *
     * This function takes a decimal odd and returns the equivalent American odd.
     * American odds are represented as a string with a '+' or '-' sign prefix.
     * Positive odds indicate the potential profit on a 100 unit stake,
     * while negative odds indicate how much needs to be staked to profit 100 units.
     *
     * @param float $decimalOdds The decimal odds to convert (must be greater than 1)
     * @return string The equivalent American odds, including the '+' or '-' sign
     * @throws InvalidArgumentException If the input is less than or equal to 1
     */
    public static function decimalToAmericanOdds(float $decimalOdds): string
    {
        if ($decimalOdds <= 1) {
            throw new \InvalidArgumentException("Decimal odds must be greater than 1");
        }

        if ($decimalOdds >= 2) {
            // For decimal odds 2.00 or greater, American odds are positive
            $americanOdds = round(($decimalOdds - 1) * 100);
            return '+' . $americanOdds;
        } else {
            // For decimal odds less than 2.00, American odds are negative
            $americanOdds = round(100 / ($decimalOdds - 1));
            return '-' . $americanOdds;
        }
    }

    /**
     * Converts American odds to decimal odds.
     *
     * This function takes American odds (either positive or negative) and returns
     * the equivalent decimal odds. American odds are typically represented as integers
     * with a '+' or '-' sign prefix, while decimal odds are represented as floats.
     *
     * @param string $americanOdds The American odds to convert (e.g., "+150" or "-200")
     * @return float The equivalent decimal odds, rounded to two decimal places
     * @throws InvalidArgumentException If the input is not a valid American odds format
     */
    public static  function americanToDecimalOdds(string $americanOdds): float
    {
        if (!preg_match('/^[+-]\d+$/', $americanOdds)) {
            throw new \InvalidArgumentException("Invalid American odds format. Must be a string starting with '+' or '-' followed by a number.");
        }
        $numericOdds = intval($americanOdds);
        if ($numericOdds > 0) {
            // Positive American odds
            $decimalOdds = ($numericOdds / 100) + 1;
        } else {
            // Negative American odds
            $decimalOdds = (100 / abs($numericOdds)) + 1;
        }
        return round($decimalOdds, 2);
    }


    /**
     * Converts decimal odds to percentage odds.
     *
     * This function takes a decimal odd and returns the equivalent percentage odd.
     * The percentage odd represents the implied probability of the event occurring.
     *
     * @param float $decimalOdds The decimal odds to convert (must be greater than 1)
     * @return float The equivalent percentage odds, rounded to two decimal places
     * @throws InvalidArgumentException If the input is less than or equal to 1
     */
    public static function decimalToPercentageOdds(float $decimalOdds): float
    {
        if ($decimalOdds <= 1) {
            throw new \InvalidArgumentException("Decimal odds must be greater than 1");
        }

        $percentageOdds = (1 / $decimalOdds) * 100;
        return round($percentageOdds, 2);
    }

    /**
     * Converts percentage odds to decimal odds.
     *
     * This function takes percentage odds (representing the implied probability of an event)
     * and returns the equivalent decimal odds. Percentage odds are typically represented
     * as a float between 0 and 100, while decimal odds are represented as a float greater than 1.
     *
     * @param float $percentageOdds The percentage odds to convert (must be greater than 0 and less than or equal to 100)
     * @return float The equivalent decimal odds, rounded to two decimal places
     * @throws InvalidArgumentException If the input is not within the valid range (0 < x <= 100)
     */
    public static function percentageToDecimalOdds(float $percentageOdds): float
    {
        if ($percentageOdds <= 0 || $percentageOdds > 100) {
            throw new \InvalidArgumentException("Percentage odds must be greater than 0 and less than or equal to 100");
        }
        $decimalOdds = 100 / $percentageOdds;
        return round($decimalOdds, 2);
    }


    /**
     * Determine amount of stake for a tradeout stake.
     * @param \App\Enums\StakeType  $type
     * @param float $originalAmount
     * @param float $originalOdds
     * @param float $newOdds
     * @return float
     */
    public static function calculateTradeOutAmount(StakeType $type, float $originalAmount, float $originalOdds, float $newOdds): float
    {
        if ($type === StakeType::BACK) {
            return $originalAmount * $originalOdds / $newOdds;
        } else { // LAY
            return $originalAmount * ($originalOdds - 1) / ($newOdds - 1);
        }
    }


    /**
     * Determine profit or loss for for a tradeout stake.
     * @param \App\Enums\StakeType  $type
     * @param float $originalAmount
     * @param float $originalOdds
     * @param float $tradeOutAmount
     * @param float $newOdds
     * @return float
     */
    public static function calculateProfitLoss(StakeType $type, float $originalAmount, float $originalOdds, float $tradeOutAmount, float $newOdds): float
    {
        if ($type === StakeType::BACK) {
            return $tradeOutAmount - $originalAmount;
        } else { // LAY
            return $originalAmount - $tradeOutAmount;
        }
    }


    /**
     * public function to match stakes that can trade.
     * @param \App\Models\Stake $stake
     * @return \App\Models\Stake
     */
    public static function matchStake(Stake $stake): Stake
    {
        $stake->load('bet');
        $oppositeType = $stake->type === StakeType::BACK ? StakeType::LAY : StakeType::BACK;
        $matchingStakes = Stake::where('bet_id', $stake->bet_id)
            ->where('type', $oppositeType)
            ->where(function (Builder $query) {
                $query->where('status',  StakeStatus::PENDING)
                    ->orWhere('status',  StakeStatus::PARTIAL);
            })
            ->where('id', '!=', $stake->id)
            ->where('odds', $stake->type === StakeType::LAY ? '<=' : '>=', $stake->odds)
            ->orderBy('odds', $stake->type === StakeType::LAY ? 'asc' : 'desc')
            ->orderBy('created_at', 'asc')
            ->get();
        foreach ($matchingStakes as $matchingStake) {
            if ($stake->unfilled <= 0) break;
            $tradeAmount = min($stake->unfilled, $matchingStake->unfilled);
            $payoutOdds = $stake->type === StakeType::BACK ? $stake->odds : $matchingStake->odds;
            $executionOdds = $stake->type === StakeType::LAY ? $stake->odds : $matchingStake->odds;
            $trade = new Trade([
                'maker_id' => $matchingStake->id,
                'taker_id' => $stake->id,
                'game_id' => $stake->game_id,
                'bet_id' => $stake->bet_id,
                'market_id' => $stake->bet->market_id,
                'amount' => $tradeAmount,
                'payout' => $tradeAmount *  $payoutOdds, // amount that this trade pays out
                'price' => $executionOdds, // price trade executes at.
                'maker_price' => $matchingStake->odds,
                'status' => TradeStatus::PENDING,
                'buy' => $stake->type === StakeType::BACK ? $stake->odds : $matchingStake->odds,
                'sell' => $stake->type === StakeType::LAY ? $stake->odds : $matchingStake->odds,
                'margin' => abs($stake->odds - $matchingStake->odds) * $tradeAmount,
            ]);
            $trade->save();
            $stake->unfilled -= $tradeAmount;
            $stake->filled += $tradeAmount;
            $matchingStake->unfilled -= $tradeAmount;
            $matchingStake->filled += $tradeAmount;
            $matchingStake->payout = TradeManager::calculatePayout($matchingStake->type, $matchingStake->filled, $matchingStake->odds, $matchingStake->liability);
            if ($matchingStake->unfilled == 0) {
                $matchingStake->status = StakeStatus::MATCHED;
            } else {
                $matchingStake->status = StakeStatus::PARTIAL;
            }
            $matchingStake->save();
        }
        $stake->payout = TradeManager::calculatePayout($stake->type, $stake->filled, $stake->odds, $stake->liability);
        if ($stake->unfilled == 0) {
            $stake->status = StakeStatus::MATCHED;
        } elseif ($stake->filled > 0) {
            $stake->status = StakeStatus::PARTIAL;
        }
        $stake->save();
        return $stake;
    }

    /**
     * public function to refund stakes that had cancelled games.
     * @param \App\Models\Stake $stake
     * @return void
     */
    public static function refundStake(Stake $stake): void
    {
        DB::beginTransaction();
        try {
            if ($stake->status == StakeStatus::GAME_CANCELLED) return;
            $stake->status = StakeStatus::GAME_CANCELLED;
            $stake->save();
            static::refundLiability($stake);
            $stake->maker_trades()->update([
                'status' => TradeStatus::CANCELLED
            ]);
            $stake->taker_trades()->update([
                'status' => TradeStatus::CANCELLED
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error settling stake: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Settle stake when game ends
     * @param \App\Models\Stake $stake
     * @param bool $betWon
     * @return void
     */

    public static function settleStake(Stake $stake, bool $betWon): void
    {
        if ($stake->is_trade_out) {
            static::settleTradedOutStake($stake, $betWon);
            return;
        }
        DB::beginTransaction();
        try {
            $stake->load(['user', 'maker_trades', 'taker_trades']);
            $stake->won = $betWon;
            $stake->status =  StakeStatus::TRADE_OUT;
            $stake->save();
            $user = $stake->user;

            // Refund unfilled amount if any
            if ($stake->unfilled > 0) {
                $unfilledLiability = TradeManager::calculateLiability($stake->type, $stake->unfilled, $stake->odds);
                $user->increment('balance', $unfilledLiability);
                $stake->transactions()->create([
                    'user_id' => $user->id,
                    'description' => "Refund unfilled amount for BET #" . $stake->id,
                    'amount' => $unfilledLiability,
                    'balance_before' => $user->balance - $unfilledLiability,
                    'action' => TransactionAction::CREDIT,
                    'type' => TransactionType::BET_REFUND
                ]);
            }
            // Exits if bet lost
            if (! $betWon) {
                DB::commit();
                return;
            }

            // Create win TX;
            $balanceBefore = $user->balance;
            $user->increment('balance', $stake->payout);
            $stake->transactions()->create([
                'user_id' => $user->id,
                'description' => "Won BET #" . $stake->id . " (Filled: {$stake->filled})",
                'amount' =>  $stake->payout,
                'balance_before' => $balanceBefore,
                'action' => TransactionAction::CREDIT,
                'type' => TransactionType::WIN
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error settling stake: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Settle a tradeout stake when game ends
     * @param \App\Models\Stake $stake
     * @param bool $betWon
     * @return void
     */
    public static function settleTradedOutStake(Stake $stake, bool $betWon): void
    {
        DB::beginTransaction();
        try {
            $stake->load(['user']);
            $stake->won = $betWon;
            $stake->status =  StakeStatus::TRADE_OUT;
            $stake->save();
            // Handle profit/loss for trade out stakes
            if ($stake->is_traded_out && $stake->profit_loss > 0) {
                $profitLoss = $stake->profit_loss;
                $user = $stake->user;
                $user->increment('balance', $profitLoss);
                $stake->transactions()->create([
                    'user_id' => $user->id,
                    'description' => "Trade out profit/loss for BET #" . $stake->id,
                    'amount' => $profitLoss,
                    'balance_before' => $user->balance - $profitLoss,
                    'action' => TransactionAction::CREDIT,
                    'type' => TransactionType::TRADE_OUT_PROFIT
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error settling stake: " . $e->getMessage());
            throw $e;
        }
    }


    /**
     * public function to collect a users liability.
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Stake $stake
     * @return void
     */
    public static function collectLiability(Stake $stake)
    {
        $user = $stake->user;
        $stake->transactions()->create([
            'user_id' => $stake->user_id,
            'description' => 'Place BET #' . $stake->id,
            'amount' => $stake->liability,
            'balance_before' => $user->balance,
            'action' => TransactionAction::DEBIT,
            'type' => TransactionType::BET
        ]);
        $user->decrement('balance', $stake->liability);
    }

    /**
     * public function to refund a users liability.
     * @param \App\Models\Stake $stake
     * @return void
     */
    public static function refundLiability(Stake $stake)
    {
        $user = $stake->user;
        $stake->transactions()->create([
            'user_id' => $stake->user_id,
            'description' => 'Refund BET #' . $stake->id,
            'amount' => $stake->liability,
            'balance_before' => $user->balance,
            'action' => TransactionAction::CREDIT,
            'type' => TransactionType::BET_REFUND
        ]);
        $user->increment('balance', $stake->liability);
    }

    /**
     * public function to determine a users liability.
     * @param \App\Enums\StakeType  $type
     * @param float $amount
     * @param float $odds
     * @return float
     */
    public static function calculateLiability(StakeType $type, float $amount, float $odds): float
    {
        if ($type === StakeType::LAY) {
            return $amount * ($odds - 1);
        }
        return $amount;
    }

    /**
     * public function to determine a users liability.
     * @param \App\Enums\StakeType  $type
     * @param float $amount
     * @param float $odds
     * @return float
     */
    public static function calculatePayout(StakeType $type, float $amount, float $odds, float $liability): float
    {
        switch ($type) {
            case StakeType::BACK:
                return $amount * $odds;
            case StakeType::LAY:
                return $amount + $liability;
            default:
                throw new \InvalidArgumentException("Invalid stake type");
        }
    }
}
