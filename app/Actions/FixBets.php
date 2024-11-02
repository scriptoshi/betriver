<?php

namespace App\Actions;

use App\Models\Game;
use App\Models\GameMarket;


class FixBets
{

    /**
     * Settles stakes site wide regardless of game
     * @return void
     */
    public static function setWinners(Game $game): Game
    {
        if ($game->bets_fixed) return $game;
        $game->load('markets.bets');
        $upserts = [];
        $sync = [];
        foreach ($game->markets as $market) {
            foreach ($market->bets as $bet) {
                $won = $market->manager()->won($game, $bet);
                if ($won) {
                    $upserts[] = ['market_id' => $market->id, 'game_id' => $game->id, 'winning_bet_id' => $bet->id];
                    $sync[] = $bet->id;
                }
            }
        }
        //sync
        $game->winBets()->sync($sync);
        // update in one query
        GameMarket::upsert($upserts, uniqueBy: ['market_id', 'game_id'], update: ['winning_bet_id']);
        $game->bets_fixed = true;
        $game->save();
        return $game;
    }
}
