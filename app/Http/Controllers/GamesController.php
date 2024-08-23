<?php

namespace App\Http\Controllers;

use App\Enums\Afl\GameStatus;
use App\Enums\LeagueSport;
use App\Http\Controllers\Controller;
use App\Http\Resources\Game as GameResource;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Database\Eloquent\Builder;
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
        $game_result = Market::query()
            ->where('is_default', true);
        $query  = Game::query()
            ->with(['scores', 'league', 'homeTeam', 'awayTeam']);
        $query->with([
            'lays' => function (Builder $q) {
                $q->whereHas('market', fn($q) => $q->where('is_default', true))
                    ->select(
                        'odds as price',
                        'game_id',
                        'bet_id',
                        DB::raw('sum(amount) as amount')
                    )
                    ->groupBy(['bet_id', 'odds',  'game_id'])
                    ->oldest('latest')
                    ->limit(3);
            },
            'backs' => function (Builder $q) {
                $q->whereHas('market', fn($q) => $q->where('is_default', true))
                    ->select(
                        'odds as price',
                        'bet_id',
                        'game_id',
                        DB::raw('sum(amount) as amount')
                    )
                    ->groupBy(['bet_id', 'odds', 'game_id'])
                    ->oldest('price')
                    ->limit(3)
                ;
            },
        ]);
        if (!empty($sport)) {
            $query->where('sport', $sport);
        }
        if (!empty($keyword)) {
            $query->where('name', 'LIKE', "%$keyword%");
        }
        if ($region)
            match ($region) {
                'live' => $query->live(),
                'today' => $query->today(),
                'tommorrow' => $query->tommorrow(),
                'week' => $query->thisWeek(),
                'next-weeks' => $query->nextWeek(),
                'region' => $query->where('country',  $country),
                default => $query->whereHas('league', fn($q) => $q->where('slug', $region))
            };
        $gamesItems = $query->latest()->paginate($perPage);
        $games = GameResource::collection($gamesItems);
        return Inertia::render('Games/Index', [
            'games' => $games,
            'sport' => $sport,
            'region' => $region,
            'country' => $country,
        ]);
    }



    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request, Game $game)
    {
        $game->load([
            'scores',
            'league',
            'homeTeam',
            'awayTeam',
            'stakes' => fn($q) => $q->where('user_id', $request->user()->id ?? null),
            'stakes' => fn($q) => $q->where('user_id', $request->user()->id ?? null),
            'tickets.wagers' => fn($q) => $q->where('user_id', $request->user()->id ?? null),
            'odds',
        ]);

        $markets = Market::query()
            ->where('sport', $game->sport)
            ->where('active', true)
            ->has('bets')
            ->withExists('odds as has_odds')
            ->with(['bets' => function (Builder $query) use ($game) {
                $query->withCount([
                    'lays' => fn($q) => $q->where('game_id', $game->id),
                    'backs' => fn($q) => $q->where('game_id', $game->id)
                ]);
                $query->withSum(['lays' => fn($q) => $q->where('game_id', $game->id)], 'amount');
                $query->withSum(['backs' => fn($q) => $q->where('game_id', $game->id)], 'amount');
                $query->with([
                    'lays' => function (Builder $q) use ($game) {
                        $q->where('game_id', $game->id)
                            ->select(
                                'odds as price',
                                'game_id',
                                'bet_id',
                                DB::raw('sum(amount) as amount')
                            )
                            ->groupBy(['odds', 'bet_id', 'game_id'])
                            ->oldest('latest')
                            ->limit(3);
                    },
                    'backs' => function (Builder $q) use ($game) {
                        $q->where('game_id', $game->id)
                            ->select(
                                'odds as price',
                                'bet_id',
                                'game_id',
                                DB::raw('sum(amount) as amount')
                            )
                            ->groupBy(['odds', 'bet_id', 'game_id'])
                            ->oldest('price')
                            ->limit(3)
                        ;
                    },
                    'last_trade' => fn($q) => $q->where('game_id', $game->id),
                    // graph data
                    'trades' => function (Builder $q) use ($game) {
                        $q->where('game_id', $game->id)
                            ->select(
                                DB::raw('AVG(odds) as probability'),
                                DB::raw('SUM(amount) as volume'),
                                'created_at as date',
                                'game_id',
                            )
                            ->groupBy(['game_id', DB::raw('DATE(created_at)'), DB::raw('HOUR(created_at)')])
                            ->oldest('price')
                            ->limit(3)
                        ;
                    }
                ]);
            }])
            ->get();


        return Inertia::render('AdminGames/Show', [
            'game' => new GameResource($game)
        ]);
    }
}
