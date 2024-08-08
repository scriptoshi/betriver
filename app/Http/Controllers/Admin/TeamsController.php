<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Uploads;
use App\Enums\LeagueSport;
use App\Http\Controllers\Controller;
use App\Http\Resources\Team as TeamResource;
use App\Models\Team;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Inertia\Inertia;

class TeamsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request, $sport = 'all')
    {
        $keyword = $request->get('search');
        $perPage = 25;
        $query  = Team::query()->with(['home_games', 'away_games']);
        if (!empty($keyword)) {
            $query->where('name', 'LIKE', "%$keyword%")
                ->orWhere('code', 'LIKE', "%$keyword%")
                ->orWhere('country', 'LIKE', "%$keyword%")
                ->orWhere('description', 'LIKE', "%$keyword%");
        }
        if ($sport && $sport != 'all') {
            $query->where('sport', $sport);
        }
        $teamsItems = $query->latest()->paginate($perPage);
        $teams = TeamResource::collection($teamsItems);
        return Inertia::render('Admin/Teams/Index', [
            'teams' => $teams,
            'sport' => fn () => strlen($sport) == 3 ? strtoupper($sport) :  ucfirst($sport),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function create(Request $request, $sport = 'all')
    {
        return Inertia::render('Admin/Teams/Create', [
            'sport' => $sport == 'all' ? null : $sport,
            'teamSports' => collect(LeagueSport::cases())->map(fn (LeagueSport $l) => [
                'label' => strlen($l->value) == 3 ? strtoupper($l->value) :  ucfirst($l->value),
                'value' => $l->value,
                'key' => $l->name
            ])
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
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
            'country' => ['required', 'string'],
            'code' => ['required', 'string'],
            'sport' => ['required', 'string', new Enum(LeagueSport::class)],
            'image_uri' => ['required', 'string'],
            'image_upload' => ['required', 'boolean'],
            'image_path' => ['nullable', 'array'],
        ]);
        $team = new Team;
        $team->teamId = null;
        $team->sport =  $request->sport;
        $team->country =  $request->country;
        $team->code =  $request->code;
        $team->name = $request->name;
        $team->description = $request->description;
        $team->save();
        $upload = app(Uploads::class)->upload($request,  $team, 'image');
        $team->image =  $upload->url;
        $team->save();
        return redirect()->route('admin.teams.index')->with('success', 'Team added!');
    }



    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, Team $team)
    {
        $team->load(['home_games', 'away_games']);
        return Inertia::render('Admin/Teams/Edit', [
            'team' => new TeamResource($team),
            'teamSports' => collect(LeagueSport::cases())->map(fn (LeagueSport $l) => [
                'label' => strlen($l->value) == 3 ? strtoupper($l->value) :  ucfirst($l->value),
                'value' => $l->value,
                'key' => $l->name
            ])
        ]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, Team $team)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
            'country' => ['required', 'string'],
            'code' => ['required', 'string'],
            'sport' => ['required', 'string', new Enum(LeagueSport::class)],
            'image_uri' => ['required', 'string'],
            'image_upload' => ['required', 'boolean'],
            'image_path' => ['nullable', 'array'],
        ]);
        $upload = app(Uploads::class)->upload($request, $team, 'image');
        $team->sport = $request->sport;
        $team->country = $request->country;
        $team->code = $request->code;
        $team->name = $request->name;
        $team->description = $request->description;
        if (isset($upload->url))
            $team->image = $upload->url;
        $team->save();
        return back()->with('success', 'Team updated!');
    }

    /**
     * toggle status of  the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function toggle(Request $request, Team $team)
    {
        $team->active = !$team->active;
        $team->save();
        return back()->with('success', $team->active ? __(' :name Team Enabled !', ['name' => $team->name]) : __(' :name  Team Disabled!', ['name' => $team->name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Request $request, Team $team)
    {
        $team->delete();
        return redirect()->route('admin.teams.index')->with('success', 'Team deleted!');
    }
}
