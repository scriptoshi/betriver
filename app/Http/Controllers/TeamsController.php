<?php
namespace App\Http\Controllers;
use App\Actions\Uploads;
use App\Http\Controllers\Controller;
use App\Http\Resources\Team as TeamResource ;
use App\Models\Team;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TeamsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request )
    {
        $keyword = $request->get('search');
        $perPage = 25;
        $query  = Team::query()->with(['home_games','away_games']);
        if (!empty($keyword)) {
            $query->where('teamId', 'LIKE', "%$keyword%")
			->orWhere('name', 'LIKE', "%$keyword%")
			->orWhere('code', 'LIKE', "%$keyword%")
			->orWhere('country', 'LIKE', "%$keyword%")
			->orWhere('description', 'LIKE', "%$keyword%")
			->orWhere('image', 'LIKE', "%$keyword%");
        } 
        $teamsItems = $query->latest()->paginate($perPage);
        $teams = TeamResource::collection($teamsItems);
        return Inertia::render('AdminTeams/Index', compact('teams'));
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return Inertia::render('AdminTeams/Create');
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request )
    {
        $request->validate([
			'name' => ['required','string'],
			'code' => ['required','string'],
			'country' => ['required','string'],
			'description' => ['required','string'],
			'image' => ['required','string'],
			'image_uri' => ['required', 'string'],
			'image_upload' => ['required', 'boolean'],
			'image_path' => ['nullable', 'string', 'required_if:image_upload,true'],
		]);
        $team = new Team;
        $team->teamId = $request->teamId;
		$team->name = $request->name;
		$team->code = $request->code;
		$team->country = $request->country;
		$team->description = $request->description;
		$team->image = $request->image;
		$team->save();
        app(Uploads::class)->upload($request,  $team, 'image');
        return redirect()->route('teams.index')->with('success', 'Team added!');
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request, Team $team)
    {
        $team->load(['home_games','away_games']);
        return Inertia::render('AdminTeams/Show', [
            'team'=> new TeamResource($team)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, Team $team)
    {
        $team->load(['home_games','away_games']);
        return Inertia::render('AdminTeams/Edit', [
            'team'=> new TeamResource($team)
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
    public function update(Request $request, Team $team)
    {
        $request->validate([
			'name' => ['required','string'],
			'code' => ['required','string'],
			'country' => ['required','string'],
			'description' => ['required','string'],
			'image' => ['required','string'],
			'image_uri' => ['required', 'string'],
			'image_upload' => ['required', 'boolean'],
			'image_path' => ['nullable', 'string', 'required_if:image_upload,true'],
		]);
        app(Uploads::class)->upload($request,  $team, 'image');
        $team->teamId = $request->teamId;
		$team->name = $request->name;
		$team->code = $request->code;
		$team->country = $request->country;
		$team->description = $request->description;
		$team->image = $request->image;
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
        return redirect()->route('teams.index')->with('success', 'Team deleted!');
    }
}
