<?php

namespace App\Http\Controllers;

use App\Enums\LeagueSport;
use App\Http\Controllers\Controller;
use App\Http\Resources\Game as GameResource;
use App\Models\Game;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GamesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 25;
        $query  = Game::query()->with(['scores', 'league', 'homeTeam', 'awayTeam', 'markets', 'stakes', 'trades', 'tickets', 'wagers', 'odds', 'winBets']);
        if (!empty($keyword)) {
            $query->where('league_id', 'LIKE', "%$keyword%")
                ->orWhere('home_team_id', 'LIKE', "%$keyword%")
                ->orWhere('away_team_id', 'LIKE', "%$keyword%")
                ->orWhere('name', 'LIKE', "%$keyword%")
                ->orWhere('startTime', 'LIKE', "%$keyword%")
                ->orWhere('endTime', 'LIKE', "%$keyword%")
                ->orWhere('status', 'LIKE', "%$keyword%")
                ->orWhere('sport', 'LIKE', "%$keyword%");
        }
        $gamesItems = $query->latest()->paginate($perPage);
        $games = GameResource::collection($gamesItems);
        return Inertia::render('AdminGames/Index', compact('games'));
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return Inertia::render('AdminGames/Create');
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $request->validate([
            'league_id' => ['required', 'integer', 'exists:leagues,id'],
            'home_team_id' => ['required', 'integer', 'exists:teams,id'],
            'away_team_id' => ['required', 'integer', 'exists:teams,id'],
            'name' => ['required', 'string'],
            'startTime' => ['required', 'datetime'],
            'endTime' => ['required', 'datetime'],
            'status' => ['required', 'string'],
            'sport' => ['required', 'string', new Enum(LeagueSport::class)],
        ]);
        $game = new Game;
        $game->league_id = $request->league_id;
        $game->home_team_id = $request->home_team_id;
        $game->away_team_id = $request->away_team_id;
        $game->name = $request->name;
        $game->startTime = $request->startTime;
        $game->endTime = $request->endTime;
        $game->status = $request->status;
        $game->sport = $request->sport;
        $game->save();

        return redirect()->route('games.index')->with('success', 'Game added!');
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request, Game $game)
    {
        $game->load(['scores', 'league', 'homeTeam', 'awayTeam', 'markets', 'stakes', 'trades', 'tickets', 'wagers', 'odds', 'winBets']);
        return Inertia::render('AdminGames/Show', [
            'game' => new GameResource($game)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, Game $game)
    {
        $game->load(['scores', 'league', 'homeTeam', 'awayTeam', 'markets', 'stakes', 'trades', 'tickets', 'wagers', 'odds', 'winBets']);
        return Inertia::render('AdminGames/Edit', [
            'game' => new GameResource($game)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, Game $game)
    {
        $request->validate([
            'league_id' => ['required', 'integer', 'exists:leagues,id'],
            'home_team_id' => ['required', 'integer', 'exists:teams,id'],
            'away_team_id' => ['required', 'integer', 'exists:teams,id'],
            'name' => ['required', 'string'],
            'startTime' => ['required', 'datetime'],
            'endTime' => ['required', 'datetime'],
            'status' => ['required', 'string'],
            'sport' => ['required', 'string', new Enum(LeagueSport::class)],
        ]);

        $game->league_id = $request->league_id;
        $game->home_team_id = $request->home_team_id;
        $game->away_team_id = $request->away_team_id;
        $game->name = $request->name;
        $game->startTime = $request->startTime;
        $game->endTime = $request->endTime;
        $game->status = $request->status;
        $game->sport = $request->sport;
        $game->save();
        return back()->with('success', 'Game updated!');
    }

    /**
     * toggle status of  the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function toggle(Request $request, Game $game)
    {
        $game->active = !$game->active;
        $game->save();
        return back()->with('success', $game->active ? __(' :name Game Enabled !', ['name' => $game->name]) : __(' :name  Game Disabled!', ['name' => $game->name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Request $request, Game $game)
    {
        $game->delete();
        return redirect()->route('games.index')->with('success', 'Game deleted!');
    }
}
