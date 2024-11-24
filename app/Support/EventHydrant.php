<?php

namespace App\Support;

use App\Actions\FixBets;
use App\Http\Resources\Game as ResourcesGame;
use App\Http\Resources\Market as ResourcesMarket;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

class EventHydrant
{

    public static function hydrateMarket(Game $game, Market $mkt): ResourcesMarket
    {
        DB::statement("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
        $rangeSize = (float) settings('odds_spread', 0.2);
        $rangeSize = $rangeSize < 0.001 ? 0.2 :  $rangeSize;
        $market = Market::query()
            ->withSum(['trades as traded' => fn($q) => $q->where('game_id', $game->id)], 'amount')
            ->withSum(['stakes as liquidity' => fn($q) => $q->where('game_id', $game->id)], 'unfilled')
            ->withSum(['stakes as volume' => fn($q) => $q->where('game_id', $game->id)], 'liability')
            ->with('gameMarkets', function (HasMany $query) use ($game) {
                $query->with('winningBet');
                $query->where('game_id', $game->id);
            })
            ->withExists(['odds as has_odds' => function ($q) use ($game) {
                $q->where('odds.game_id', $game->id);
            }])
            ->with(['bets' => function (HasMany $query) use ($game, $rangeSize) {
                $query->withExists(['odds as has_odds' => function ($q) use ($game) {
                    $q->where('odds.game_id', $game->id);
                }])
                    ->with(['odds' => fn($q) => $q->where('game_id', $game->id)])
                    ->with([
                        'last_trade' => function (HasOne $query) use ($game) {
                            $query->where('game_id', $game->id);
                        },
                        // graph data
                        'trades' => function (HasMany $q) use ($game) {
                            $q->where('game_id', $game->id)
                                ->select(
                                    'bet_id',
                                    DB::raw('FLOOR(UNIX_TIMESTAMP(created_at) / (5 * 60)) AS time_range'),
                                    DB::raw('FROM_UNIXTIME(FLOOR(UNIX_TIMESTAMP(created_at) / (5 * 60)) * (5 * 60)) AS range_start'),
                                    DB::raw('AVG(price) AS avg_price'),
                                    DB::raw('SUM(amount) AS total_amount'),
                                )
                                ->groupBy(['bet_id', 'time_range']);
                        }
                    ]);
                $query->with([
                    'lays' => function (HasMany $q) use ($game, $rangeSize) {
                        $q->where('game_id', $game->id)
                            ->select(
                                'bet_id',
                                DB::raw("FLOOR(odds / $rangeSize) AS range_code"),
                                DB::raw('SUM(unfilled) AS amount'),
                                DB::raw('MAX(odds) as price')
                            )
                            ->groupBy('bet_id')
                            ->groupBy(DB::raw("FLOOR(odds / $rangeSize)"))
                            ->latest(DB::raw('MAX(odds)'))
                            ->limit(3);
                    },
                    'backs' => function (HasMany $q) use ($game, $rangeSize) {
                        $q->where('game_id', $game->id)
                            ->select(
                                'bet_id',
                                DB::raw("FLOOR(odds / $rangeSize) AS range_code"),
                                DB::raw('SUM(unfilled) AS amount'),
                                DB::raw('MIN(odds) as price')
                            )
                            ->groupBy('bet_id')
                            ->groupBy(DB::raw("FLOOR(odds / $rangeSize)"))
                            ->oldest(DB::raw('MAX(odds)'))
                            ->limit(3);
                    }
                ]);
            }])->find($mkt->id);
        DB::statement("SET SESSION sql_mode=(SELECT CONCAT(@@sql_mode, ',ONLY_FULL_GROUP_BY'));");
        return new ResourcesMarket($market);
    }

    /**
     * load the game elements for the frontend
     */
    public static function hydrateGame(Game $game, Market $defaultMarket): ResourcesGame
    {

        $query  = Game::query()
            ->withSum('trades as traded', 'amount')
            ->with([
                'scores',
                'league',
                'homeTeam',
                'awayTeam',
                'odds' => fn($q) => $q->where('market_id', $defaultMarket->id)
            ])
            ->withCount('activeMarkets as marketsCount')
            ->withExists(['odds as has_odds' => fn($q) => $q->where('market_id', $defaultMarket->id)])
            ->latest('traded');
        DB::statement("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
        $rangeSize = (float) settings('odds_spread', 0.2);
        $rangeSize = $rangeSize < 0.001 ? 0.2 :  $rangeSize;
        $query->with([
            'lays' => function (HasMany $q) use ($defaultMarket, $rangeSize) {
                $q->where('market_id', $defaultMarket->id)
                    ->select(
                        'bet_id',
                        'game_id',
                        DB::raw("FLOOR(odds / $rangeSize) AS range_code"),
                        DB::raw('SUM(unfilled) AS amount'),
                        DB::raw('MAX(odds) as price')
                    )
                    ->groupBy(['bet_id', 'game_id'])
                    ->groupBy(DB::raw("FLOOR(odds / $rangeSize)"))
                    ->latest(DB::raw('MAX(odds)'))
                    ->limit(3);
            },
            'backs' => function (HasMany $q) use ($defaultMarket, $rangeSize) {
                $q->where('market_id', $defaultMarket->id)
                    ->select(
                        'bet_id',
                        'game_id',
                        DB::raw("FLOOR(odds / $rangeSize) AS range_code"),
                        DB::raw('SUM(unfilled) AS amount'),
                        DB::raw('MIN(odds) as price')
                    )
                    ->groupBy(['bet_id', 'game_id'])
                    ->groupBy(DB::raw("FLOOR(odds / $rangeSize)"))
                    ->oldest(DB::raw('MAX(odds)'))
                    ->limit(3);;
            },
        ]);
        $game = $query->find($game->id);
        $game = new ResourcesGame($game);
        return $game;
    }

    /**
     * load the game elements for the frontend
     */
    public static function hydrate(Game $game): ResourcesGame
    {

        $game = Game::query()
            ->with(['scores', 'league', 'homeTeam', 'awayTeam'])
            ->withSum('stakes as traded', 'filled')
            ->withSum('stakes as liquidity', 'unfilled')
            ->withSum('stakes as volume', 'liability')
            ->withCount('markets as marketsCount')
            ->find($game->id);
        $game =  FixBets::setWinners($game);
        return new ResourcesGame($game);
    }
}
