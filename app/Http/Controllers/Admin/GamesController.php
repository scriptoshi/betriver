<?php

namespace App\Http\Controllers\Admin;

use App\Enums\GameStatus;
use App\Enums\LeagueSport;
use App\Http\Controllers\Controller;
use App\Http\Resources\Game as GameResource;
use App\Models\Game;
use App\Models\League;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class GamesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request, $sport = 'all')
    {
        $keyword = $request->get('search');
        $status = $request->get('status');
        $lid = $request->get('lid');
        $perPage = 25;
        $query  = Game::query()
            ->with(['league', 'homeTeam', 'awayTeam'])
            ->withSum('tickets', 'amount');
        if (!empty($keyword)) {
            $query->where('name', 'LIKE', "%$keyword%");
        }
        if (!empty($status)) {
            $query->where('status', $status);
        }
        if (!empty($lid)) {
            $query->where('league_id', $lid);
        }
        if ($sport && $sport != 'all') {
            $query->where('sport', $sport);
        }
        $gamesItems = $query->latest()->paginate($perPage);
        $games = GameResource::collection($gamesItems);
        return Inertia::render('Admin/Games/Index', [
            'games' => $games,
            'league' => League::find($lid)?->name,
            'sport' => fn () => strlen($sport) == 3 ? strtoupper($sport) :  ucfirst($sport),
            'statuses' =>  collect(GameStatus::cases())->map(function (GameStatus $status) {
                return [
                    'label' => $status->statusText(),
                    'value' => $status->value,
                    'key' => $status->name
                ];
            })
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function create(Request $request, $sport = 'all')
    {
        $leagueSport =  $sport == 'all' ? LeagueSport::from('football') : LeagueSport::from($sport);
        return Inertia::render('Admin/Games/Create', [
            'sport' => fn () => strlen($sport) == 3 ? strtoupper($sport) :  ucfirst($sport),
            'leagues' => function () use ($leagueSport) {
                $leagues = League::query()
                    ->where('active', true)
                    ->where('sport', $leagueSport)
                    ->get();
                return  $leagues->map(fn (League $lg) => [
                    'label' => $lg->name,
                    'value' => $lg->id,
                    'key' => $lg->id,
                    'image' => $lg->image,
                ]);
            },
            'teams' => function () use ($leagueSport) {
                $teams = Team::query()
                    ->where('active', true)
                    ->where('sport', $leagueSport)
                    ->get();
                return  $teams->map(fn (Team $team) => [
                    'label' => $team->name,
                    'value' => $team->id,
                    'key' => $team->id,
                    'image' => $team->image,
                ]);
            },
            'statuses' =>  collect(GameStatus::cases())->map(function (GameStatus $status) {
                return [
                    'label' => $status->statusText(),
                    'value' => $status->value,
                    'key' => $status->name
                ];
            })
        ]);
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
            'startTime' => ['required'],
            'status' => ['required', 'string', new Enum(GameStatus::class)]
        ]);
        if ($request->home_team_id == $request->away_team_id)
            throw ValidationException::withMessages(['away_team_id' => [__('Cannot be the same as home team')]]);
        $league = League::find($request->league_id);
        $home_team = Team::find($request->home_team_id);
        $away_team = Team::find($request->away_team_id);
        $game = new Game;
        $game->league_id = $request->league_id;
        $game->sport =  $league->sport;
        $game->home_team_id = $request->home_team_id;
        $game->away_team_id = $request->away_team_id;
        $game->name =  "{$home_team->name} vs {$away_team->name}";
        $game->startTime = Carbon::parse($request->startTime);
        $game->endTime =  $game->startTime->addMinutes(100);
        $game->status = $request->status ?? GameStatus::NotStarted;
        $game->save();
        return redirect()->route('admin.games.index')->with('success', 'Game added!');
    }



    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, Game $game)
    {
        $game->load(['league', 'homeTeam', 'awayTeam',]);
        $leagueSport = $game->league->sport;
        return Inertia::render('Admin/Games/Edit', [
            'game' => new GameResource($game),
            'leagues' => function () use ($leagueSport) {
                $leagues = League::query()
                    ->where('active', true)
                    ->where('sport', $leagueSport)
                    ->get();
                return  $leagues->map(fn (League $lg) => [
                    'label' => $lg->name,
                    'value' => $lg->id,
                    'key' => $lg->id,
                    'image' => $lg->image,
                ]);
            },
            'teams' => function () use ($leagueSport) {
                $teams = Team::query()
                    ->where('active', true)
                    ->where('sport', $leagueSport)
                    ->get();
                return  $teams->map(fn (Team $team) => [
                    'label' => $team->name,
                    'value' => $team->id,
                    'key' => $team->id,
                    'image' => $team->image,
                ]);
            },
            'statuses' =>  collect(GameStatus::cases())->map(function (GameStatus $status) {
                return [
                    'label' => $status->statusText(),
                    'value' => $status->value,
                    'key' => $status->name
                ];
            })
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
            'startTime' => ['required'],
            'status' => ['required', 'string', new Enum(GameStatus::class)]
        ]);
        $league = League::find($request->league_id);
        $home_team = Team::find($request->home_team_id);
        $away_team = Team::find($request->away_team_id);
        $game->league_id = $request->league_id;
        $game->sport =  $league->sport;
        $game->home_team_id = $request->home_team_id;
        $game->away_team_id = $request->away_team_id;
        $game->name =  "{$home_team->name} vs {$away_team->name}";
        $game->startTime = Carbon::parse($request->startTime);
        $game->endTime =  $game->startTime->addMinutes(100);
        $game->status = $request->status ?? GameStatus::NotStarted;
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
