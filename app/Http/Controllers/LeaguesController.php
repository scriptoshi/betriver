<?php
namespace App\Http\Controllers;
use App\Actions\Uploads;
use App\Http\Controllers\Controller;
use App\Http\Resources\League as LeagueResource ;
use App\Models\League;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LeaguesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request )
    {
        $keyword = $request->get('search');
        $perPage = 25;
        $query  = League::query()->with(['games']);
        if (!empty($keyword)) {
            $query->where('leagueId', 'LIKE', "%$keyword%")
			->orWhere('name', 'LIKE', "%$keyword%")
			->orWhere('description', 'LIKE', "%$keyword%")
			->orWhere('image', 'LIKE', "%$keyword%");
        } 
        $leaguesItems = $query->latest()->paginate($perPage);
        $leagues = LeagueResource::collection($leaguesItems);
        return Inertia::render('AdminLeagues/Index', compact('leagues'));
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return Inertia::render('AdminLeagues/Create');
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
			'description' => ['required','string'],
			'image' => ['required','string'],
			'image_uri' => ['required', 'string'],
			'image_upload' => ['required', 'boolean'],
			'image_path' => ['nullable', 'string', 'required_if:image_upload,true'],
		]);
        $league = new League;
        $league->leagueId = $request->leagueId;
		$league->name = $request->name;
		$league->description = $request->description;
		$league->image = $request->image;
		$league->save();
        app(Uploads::class)->upload($request,  $league, 'image');
        return redirect()->route('leagues.index')->with('success', 'League added!');
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request, League $league)
    {
        $league->load(['games']);
        return Inertia::render('AdminLeagues/Show', [
            'league'=> new LeagueResource($league)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, League $league)
    {
        $league->load(['games']);
        return Inertia::render('AdminLeagues/Edit', [
            'league'=> new LeagueResource($league)
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
    public function update(Request $request, League $league)
    {
        $request->validate([
			'name' => ['required','string'],
			'description' => ['required','string'],
			'image' => ['required','string'],
			'image_uri' => ['required', 'string'],
			'image_upload' => ['required', 'boolean'],
			'image_path' => ['nullable', 'string', 'required_if:image_upload,true'],
		]);
        app(Uploads::class)->upload($request,  $league, 'image');
        $league->leagueId = $request->leagueId;
		$league->name = $request->name;
		$league->description = $request->description;
		$league->image = $request->image;
		$league->save();
        return back()->with('success', 'League updated!');
    }

     /**
     * toggle status of  the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function toggle(Request $request, League $league)
    {
        $league->active = !$league->active;
        $league->save();
        return back()->with('success', $league->active ? __(' :name League Enabled !', ['name' => $league->name]) : __(' :name  League Disabled!', ['name' => $league->name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Request $request, League $league)
    {
        $league->delete();
        return redirect()->route('leagues.index')->with('success', 'League deleted!');
    }
}
