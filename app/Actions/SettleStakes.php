<?php

namespace App\Actions;

use App\Enums\StakeStatus;
use App\Models\Game;
use App\Models\Stake;
use App\Support\TradeManager;
use Exception;

class SettleStakes
{

    /**
     * Settles stakes site wide regardless of game
     * @return void
     */
    public function execute()
    {
        $stakes = Stake::with(['maker_trades', 'taker_trades', 'bet', 'market'])
            ->withWhereHas(
                'game',
                fn($query) => $query
                    ->where('closed', true)
            )
            ->whereIn('status', [StakeStatus::MATCHED->value, StakeStatus::PARTIAL->value])
            ->get();
        foreach ($stakes as $stake) {
            $state = $stake->game->state();

            if ($state->cancelled()) {
                TradeManager::refundStake($stake);
                continue;
            }
            
            if ($state->finished()) {
                $betWon = $stake->market->manager()->won($stake->game, $stake->bet);
                TradeManager::settleStake($stake, $betWon); // 
                continue;
            }
        }
    }

    /**
     * Settles stakes  of a game
     * @return void
     */
    public function byGame(Game $game)
    {
        if (!$game->closed) throw new Exception(__('Game is not ended'));
        $stakes = $game->stakes()
            ->with(['maker_trades', 'taker_trades', 'bet', 'market'])
            ->whereIn('status', [StakeStatus::MATCHED->value, StakeStatus::PARTIAL->value])
            ->get();
        foreach ($stakes as $stake) {
            $state = $game->state();
            if ($state->cancelled()) {
                TradeManager::refundStake($stake);
                continue;
            }
            if ($state->finished()) {
                $betWon = $stake->market->manager()->won($stake->game, $stake->bet);
                TradeManager::settleStake($stake, $betWon); // 
                continue;
            }
        }
    }
}
