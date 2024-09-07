<?php

namespace App\Http\Controllers;

use App\Enums\LeagueSport;
use App\Http\Controllers\Controller;
use App\Http\Resources\Game as GameResource;
use App\Http\Resources\Market as ResourcesMarket;
use App\Http\Resources\Slider as ResourcesSlider;
use App\Models\Game;
use App\Models\Market;
use App\Models\Slider;
use App\Support\TradeManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

use function Clue\StreamFilter\fun;

class HomeController extends Controller
{
    /**
     * Display a listing of the games on homepage.
     * @return \Illuminate\View\View
     */
    public function index(Request $reques)
    {

        $multiples  = TradeManager::multiples();
        $defaultMarketsIds = Market::with(['bets'])
            ->where('is_default', true)
            ->pluck('id')->all();
        $sport = LeagueSport::FOOTBALL;
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
                'odds' => fn($q) => $q->where('odds.market_id', $defaultMarket->id)
            ])
            ->when($multiples, function ($query) use ($defaultMarket) {
                $query->whereHas('odds', function ($q) use ($defaultMarket) {
                    $q->where('odds.market_id', $defaultMarket->id);
                });
            })
            ->withCount('activeMarkets as marketsCount')
            ->withExists(['odds as has_odds' => fn($q) => $q->where('market_id', $defaultMarket->id)])
            ->latest('traded')
            ->whereHas('league', function (Builder $query) {
                $query->where('active', true);
            });
        /**
         * Requires to be updated to use ranges!!
         */
        $query->with([
            'lays' => function (HasMany $q) use ($defaultMarket) {
                $q->where('market_id', $defaultMarket->id)
                    ->select(
                        'game_id',
                        'bet_id',
                        DB::raw('sum(amount) as amount'),
                        DB::raw('min(odds) as price')
                    )
                    ->groupBy(['bet_id', 'game_id'])
                    ->limit(3);
            },
            'backs' => function (HasMany $q) use ($defaultMarket) {
                $q->where('market_id', $defaultMarket->id)->select(
                    'bet_id',
                    'game_id',
                    DB::raw('sum(amount) as amount'),
                    DB::raw('max(odds) as price')
                )
                    ->groupBy(['bet_id', 'game_id'])
                    ->limit(3)
                ;
            },
        ]);
        $query->inNext7Days();
        $gamesItems = $query->latest('startTime')->take(5)->get();
        return Inertia::render('Home', [
            'enableExchange' =>  settings('site.enable_exchange'),
            'enableBookie' => settings('site.enable_bookie'),
            'defaultMarkets' => ResourcesMarket::collection(
                Market::with(['bets'])
                    ->where('is_default', true)
                    ->get()
            )->keyBy('sport'),
            'defaultMarket' => new ResourcesMarket($defaultMarket),
            'games' => GameResource::collection($gamesItems),
            'slides' => fn() => ResourcesSlider::collection(Slider::where('active', true)->latest()->take(3)->get()),
            'popular' => fn() => static::popular(),
            'top' => function () use ($defaultMarketsIds,  $multiples) {
                $query = Game::query()
                    ->with([
                        'scores',
                        'league',
                        'homeTeam',
                        'awayTeam',
                        'odds' => fn($q) => $q->whereIn('market_id', $defaultMarketsIds)
                    ])
                    ->whereHas('league', function (Builder $query) {
                        $query->where('active', true);
                    })
                    ->when($multiples, function ($query) use ($defaultMarketsIds) {
                        $query->whereHas('odds', fn($q) => $q->whereIn('market_id', $defaultMarketsIds));
                    })
                    ->withSum('trades as traded', 'amount');
                //->where('startTime', '>=', now()->subHour(2));
                $query->latest('traded')
                    //->inRandomOrder()
                    ->take(7); // Never increase this. N+1 problem below.
                $games = $query->get();
                // we cant avoid n+1 loop for this query.
                $games->map(function (Game $game) use ($defaultMarketsIds) {
                    $game->load(['trades' => function ($query) use ($defaultMarketsIds, $game) {
                        $query->whereIn('market_id', $defaultMarketsIds)
                            ->select('*')
                            ->join(DB::raw("(Select max(id) as id from trades where game_id = {$game->id} group by bet_id) LatestTrades"), function ($join) {
                                $join->on('trades.id', '=', 'LatestTrades.id');
                            })
                            ->orderBy('created_at', 'desc');
                    }]);
                });
                return GameResource::collection($games);
            },
        ]);
    }




    protected static function popular($top = false)
    {
        $multiples  = TradeManager::multiples();
        $query = Game::query()
            ->with(['scores', 'league', 'homeTeam', 'awayTeam'])
            ->whereHas('league', function (Builder $query) {
                $query->where('active', true);
            })
            ->withSum('stakes as traded', 'filled')
            ->where(function (Builder $query) {
                $query->where('startTime', '>=', now()->subHour(2));
            });
        if ($top)
            $query->oldest('traded')
                ->inRandomOrder()
                ->take(7);
        else
            $query->oldest('startTime')
                ->take(13);
        $query->when($multiples, function ($query) {
            $query->whereHas('odds');
        });
        $games = $query->get();
        return GameResource::collection($games);
    }
}
