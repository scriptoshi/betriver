<?php

namespace App\Http\Controllers;

use App\Actions\FixBets;
use App\Enums\LeagueSport;
use App\Http\Controllers\Controller;
use App\Http\Resources\Game as GameResource;
use App\Http\Resources\Market as ResourcesMarket;
use App\Models\Game;
use App\Models\League;
use App\Models\Market;
use App\Support\TradeManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class GamesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request, LeagueSport $sport = null, string $region = null, $country = null)
    {

        $keyword = $request->get('search');
        $perPage = 25;
        $multiples =  TradeManager::multiples();
        $defaultMarket = Market::with(['bets'])
            ->where('sport', $sport)
            ->where('is_default', true)
            ->first();
        $query  = Game::query()
            ->where('active', true)
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
            ->latest('traded')
            ->whereHas('league', function (Builder $query) {
                $query->where('active', true);
            });

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
        if (!empty($sport)) {
            $query->where('sport', $sport);
        }
        if (!empty($keyword)) {
            $query->where('name', 'LIKE', "%$keyword%");
        }
        if ($multiples) {
            $query->whereHas('odds');
        }
        if ($region) {
            match ($region) {
                'live' => $query->live(),
                'today' => $query->today(),
                'ended' => $query->where('closed', true),
                'tomorrow' => $query->tomorrow(),
                'this-week' => $query->thisWeek(),
                'next-week' => $query->nextWeek(),
                'region' => $query->where('country',  $country),
                default => $query->whereHas('league', fn($q) => $q->where('slug', $region))
            };
        } else {
            $query->whereDate('startTime', '>=', now());
        }
        $gamesItems = $query->latest('startTime')->paginate($perPage)->onEachSide(1);
        DB::statement("SET SESSION sql_mode=(SELECT CONCAT(@@sql_mode, ',ONLY_FULL_GROUP_BY'));");
        $games = GameResource::collection($gamesItems);
        $league =  match ($region) {
            'live',
            'today',
            'tommorrow',
            'week',
            'next-weeks',
            'region' => null,
            default => value(fn() => League::where('slug', $region)->first())
        };
        return Inertia::render('Games/Index', [
            'defaultMarketsCount' => $sport ? Market::where('sport', $sport)->pluck('id')->count() : 0,
            'defaultMarket' => Market::with(['bets'])
                ->where('sport', $sport)
                ->where('is_default', true)
                ->first(),
            'games' => $games,
            'sport' => $sport,
            'league' => $league,
            'region' => $region,
            'enableExchange' =>  settings('site.enable_exchange'),
            'enableBookie' => settings('site.enable_bookie'),
            'country' => function () use ($country, $league) {
                if ($country)
                    return  $country;
                return  $league?->country ?? null;
            }
        ]);
    }

    /**
     * Add to watch list.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addToWatchlist(Request $request, Game $game)
    {
        $request->user()
            ->watchlist()
            ->syncWithoutDetaching([$game->id]);
        return back();
    }

    /**
     * Remove from watch list.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeFromWatchlist(Request $request, Game $game)
    {
        $request->user()
            ->watchlist()
            ->detach($game->id);
        return back();
    }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function watchlist(Request $request)
    {
        $defaultMarkets = Market::with(['bets'])
            ->where('is_default', true)
            ->pluck('id')->all();
        $query  = $request->user()
            ->watchlist()
            ->where('active', true)
            ->withSum('trades as traded', 'amount')
            ->with([
                'scores',
                'league',
                'homeTeam',
                'awayTeam',
                'odds' => fn($q) => $q->whereIn('market_id', $defaultMarkets)
            ])
            ->withCount('activeMarkets as marketsCount')
            ->withExists(['odds as has_odds' => fn($q) => $q->whereIN('market_id', $defaultMarkets)])
            ->latest('traded')
            ->whereHas('league', function (Builder $query) {
                $query->where('active', true);
            });
        DB::statement("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
        $rangeSize = (float) settings('odds_spread', 0.2);
        $rangeSize = $rangeSize < 0.001 ? 0.2 :  $rangeSize;
        $query->with([
            'lays' => function (HasMany $q) use ($defaultMarkets, $rangeSize) {
                $q->whereIn('market_id', $defaultMarkets)
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
            'backs' => function (HasMany $q) use ($defaultMarkets, $rangeSize) {
                $q->whereIn('market_id', $defaultMarkets)
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
        $gamesItems = $query->latest('startTime')->take(30)->get();
        DB::statement("SET SESSION sql_mode=(SELECT CONCAT(@@sql_mode, ',ONLY_FULL_GROUP_BY'));");
        $games = GameResource::collection($gamesItems);
        return Inertia::render('Games/WatchList', [
            'defaultMarketsCounts' =>   Market::query()
                ->selectRaw('sport, COUNT(*) as total')
                ->groupBy('sport')
                ->get()
                ->pluck('total', 'sport'),
            'defaultMarkets' => Market::with(['bets'])
                ->where('is_default', true)
                ->get()
                ->keyBy('sport'),
            'games' => $games,
            'enableExchange' =>  settings('site.enable_exchange'),
            'enableBookie' => settings('site.enable_bookie'),
            'multiples' =>  TradeManager::multiples()
        ]);
    }


    /**
     * XHR Search Input for games based on user input.
     *
     * This function searches for games whose names match the given query.
     * Results are limited to 5 games and sorted by start time.
     *
     * @param \Illuminate\Http\Request $request The incoming request object
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection A collection of GameResource objects
     */
    public function filter(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection|array
    {
        $search = $request->input('search');
        if (strlen($search) < 3) return [];
        $games = Game::query()
            ->with(['scores', 'league', 'homeTeam', 'awayTeam'])
            ->where(function (Builder $query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('name', 'LIKE', "%{$search}%");
            })
            ->where(function (Builder $query) {
                $query->where('startTime', '>=', now())
                    ->orWhere('endTime', '>=', now()->subHour());
            })
            ->orderBy('startTime')
            ->take(5)
            ->get();
        return GameResource::collection($games);
    }
    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request, Game $game)
    {

        $multiples =  TradeManager::multiples();
        $game->load([
            'scores',
            'league',
            'homeTeam',
            'awayTeam'
        ]);
        $game->loadSum('trades as traded', 'amount');
        $game->loadSum('stakes as liquidity', 'unfilled');
        $game->loadSum('stakes as volume', 'liability');

        // Force sync all markets
        if ($game->markets()->count() == 0) {
            $marketIds = Market::query()
                ->where('sport', $game->sport)
                ->where('active', true)
                ->pluck('id')
                ->all();
            $game->markets()->sync($marketIds);
        }
        $game->loadCount('markets as marketsCount');

        /**
         * game ended?
         * lets ensure winning bets are marked.
         */
        if ($game->closed)
            $game = FixBets::setWinners($game);
        /**
         * We shall group trades in ranges of 0.2.
         */

        DB::statement("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
        $rangeSize = (float) settings('odds_spread', 0.2);
        $rangeSize = $rangeSize < 0.001 ? 0.2 :  $rangeSize;

        $markets = $game->markets()
            ->where('markets.active', true)
            ->withSum(['trades as traded' => fn($q) => $q->where('game_id', $game->id)], 'amount')
            ->withSum(['stakes as liquidity' => fn($q) => $q->where('game_id', $game->id)], 'unfilled')
            ->withSum(['stakes as volume' => fn($q) => $q->where('game_id', $game->id)], 'liability')
            ->with('gameMarkets', function (HasMany $query) use ($game) {
                $query->with('winningBet');
                $query->where('game_id', $game->id);
            })
            ->when($multiples, function ($query) use ($game) {
                $query->whereHas('odds', function ($q) use ($game) {
                    $q->where('odds.game_id', $game->id);
                });
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
            }])
            ->oldest('sequence')
            ->get();
        DB::statement("SET SESSION sql_mode=(SELECT CONCAT(@@sql_mode, ',ONLY_FULL_GROUP_BY'));");
        return Inertia::render('Games/Show', [
            'game' => new GameResource($game),
            'winBets' => $game->winBets()->pluck('bets.id'),
            'markets' => ResourcesMarket::collection($markets),
            'handicaps' => $game->sport->handicaps(),
            'asianhandicaps' => $game->sport->asianhandicaps(),
            'overunders' => $game->sport->overunders(),
            'categories' => $game->sport->categories(),
            'enableExchange' =>  settings('site.enable_exchange'),
            'enableBookie' => settings('site.enable_bookie'),
        ]);
    }
}
