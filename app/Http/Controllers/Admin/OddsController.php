<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Market as ResourcesMarket;
use App\Http\Resources\Odd as OddResource;
use App\Models\Bet;
use App\Models\BookieGameMarket;
use App\Models\Game;
use App\Models\GameMarket;
use App\Models\Market;
use App\Models\Odd;
use Inertia\Inertia;

use Illuminate\Http\Request;

class OddsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request, Game $game)
    {
        $countMarkets = $game->markets()->count();
        $markets = Market::query()
            ->where('sport', $game->sport)->count();
        if ($countMarkets < $markets) {
            $marketIds = Market::query()
                ->where('sport', $game->sport)
                ->where('active', true)
                ->pluck('id')
                ->all();
            $game->markets()->sync($marketIds);
        }
        $bets = Bet::where('sport', $game->sport)->get();
        $odds = $game->odds()->pluck('odd', 'md5');

        $oddActive = $game->odds()->pluck('active', 'md5');
        $bookies = $game->odds()->pluck('bookie', 'md5');
        $oddsList = $bets->map(
            function (Bet $bet) use ($game, $odds, $bookies, $oddActive) {
                $md5 = md5($bet->market_id . '-' . $bet->id . '-' . $game->id);
                return [
                    'label' =>  str($bet->name)->replace(['{home}', '{away}'], [
                        $game->homeTeam->name,
                        $game->awayTeam->name,
                    ]),
                    'md5' => $md5,
                    'bet_id' => $bet->id,
                    'game_id' => $game->id,
                    'game_type' => $game->getMorphClass(),
                    'market_id' => $bet->market_id,
                    'league_id' => $game->league_id,
                    'odd' => $odds->get($md5) ?? 0,
                    'bookie' => $bookies->get($md5) ?? null,
                    'active' => $oddActive->get($md5) ?? true
                ];
            }
        );
        return Inertia::render('Admin/Odds/Index', [
            'game' => $game,
            'markets' => function () use ($game, $oddsList) {
                $odds = $oddsList->groupBy('market_id');
                $markets = $game->markets()->get();
                return  $markets->map(function (Market $market)  use ($game, $odds): array {
                    return [
                        'id' => $market->id,
                        'gm' => $market->pivot->id,
                        'busy' => false, // javascript use
                        'active' => boolval($market->pivot->active),
                        'bookie_active' => boolval($market->pivot->bookie_active),
                        'name' =>  str($market->name)->replace(['{home}', '{away}'], [
                            $game->homeTeam->name,
                            $game->awayTeam->name,
                        ]),
                        'odds' => $odds->get($market->id)
                    ];
                });
            },
            'odds' => $oddsList->keyBy('md5')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $odds = collect($request->all())->values()->map(function ($odd) {
            return collect($odd)->only([
                'bet_id',
                'game_id',
                'game_type',
                'market_id',
                'oddId',
                'odd',
                'md5',
                'active',
            ])->all();
        })->all();
        Odd::upsert($odds, uniqueBy: ['md5'], update: ['odd']);
        return back()->with('success', 'Odd added!');
    }

    /**s
     * toggle status of  the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function toggle(Request $request, GameMarket $gameMarket)
    {

        $gameMarket->active = !$gameMarket->active;
        $gameMarket->save();
        return back()->with('success', $gameMarket->active ? __('Market Enabled in Exchange !') : __('Market Disabled in Exchange!'));
    }

    /**s
     * toggle status of market in bookie bets
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function toggleBookie(Request $request, GameMarket $gameMarket)
    {
        $gameMarket->bookie_active = !$gameMarket->bookie_active;
        $gameMarket->save();
        return back()->with('success', $gameMarket->bookie_active ? __('Market Enabled in sports book !') : __('Market Disabled in sports book!'));
    }
}
