<?php

namespace App\Http\Controllers\Admin;

use App\Enums\GameStatus;
use App\Enums\LeagueSport;
use App\Http\Controllers\Controller;
use App\Http\Resources\Game as GameResource;
use App\Http\Resources\League as ResourcesLeague;
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
        $country = $request->get('country');
        $day = $request->get('day');
        $odds = $request->get('odds');
        $scores = $request->get('scores');
        $perPage = 25;
        $query  = Game::query()
            ->with([
                'league',
                'homeTeam',
                'awayTeam'
            ])
            ->withExists('odds as has_odds')
            ->withExists('scores as has_scores')
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
        if (!empty($country)) {
            $query->whereHas('league', function ($query) use ($country) {
                $query->where('country', $country);
            });
        }
        if (!empty($odds)) {
            $query->whereHas('odds');
        }
        if (!empty($scores)) {
            $query->whereHas('scores');
        }
        if ($sport && $sport != 'all') {
            $query->where('sport', $sport);
        }
        if ($day) {
            switch ($day) {
                case 'today':
                    $query->whereDate('startTime', today());
                    break;
                case 'tomorrow':
                    $query->whereDate('startTime', today()->addDay());
                    break;
                case '2-days':
                case '3-days':
                case '4-days':
                case '5-days':
                case '6-days':
                    $daysToAdd = (int) substr($day, 0, 1);
                    $query->whereDate('startTime', today()->addDays($daysToAdd));
                    break;
                case '1-week':
                    $query->whereDate('startTime', today()->addWeek());
                    break;
                case 'yesterday':
                    $query->whereDate('startTime', today()->subDay());
                    break;
                case '2-days-ago':
                case '3-days-ago':
                case '4-days-ago':
                case '5-days-ago':
                case '6-days-ago':
                    $daysToSubtract = substr($day, 0, 1);
                    $query->whereDate('startTime', (int)today()->subDays($daysToSubtract));
                    break;
                case '1-week-ago':
                    $query->whereDate('startTime', today()->subWeek());
                    break;
            }
        }
        $gamesItems = $query->oldest('startTime')->paginate($perPage)->withQueryString();
        $games = GameResource::collection($gamesItems);

        return Inertia::render('Admin/Games/Index', [
            'games' => $games,
            'league' => League::find($lid)?->name,
            'leagues' => function () use ($sport, $country) {
                $query = League::query();
                if ($sport && $sport != 'all') {
                    $query->where('sport', $sport);
                }
                if (!empty($country)) {
                    $query->where('country', $country);
                }
                $leagues = $query->has('games')->get();
                return $leagues->map(function (League $league) {
                    return [
                        'label' => $league->name,
                        'value' => $league->id,
                        'key' => $league->id
                    ];
                });
            },
            'sport' => fn() => strlen($sport) == 3 ? strtoupper($sport) :  ucfirst($sport),
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
            'sport' => fn() => strlen($sport) == 3 ? strtoupper($sport) :  ucfirst($sport),
            'leagues' => function () use ($leagueSport) {
                $leagues = League::query()
                    ->where('active', true)
                    ->where('sport', $leagueSport)
                    ->get();
                return  $leagues->map(fn(League $lg) => [
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
                return  $teams->map(fn(Team $team) => [
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
                return  $leagues->map(fn(League $lg) => [
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
                return  $teams->map(fn(Team $team) => [
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
     * toggle status of  the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function loadFromApi(Request $request)
    {
        $request->validate([
            'sport' => ['required', 'string', new Enum(LeagueSport::class)],
            'start' => ['integer', 'required'],
            'days' => ['integer', 'required', 'min:1'],
        ]);
        $apiKey = settings('site.apifootball_api_key');
        if (empty($apiKey)) throw ValidationException::withMessages(['start' => ['Missing api key']]);
        LeagueSport::from($request->sport)->api()::loadGames($request->start, $request->days);
        // cleare menu cache
        League::clearCache();
        Game::clearCache();
        return back()->with('success', 'Games Loaded');
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
