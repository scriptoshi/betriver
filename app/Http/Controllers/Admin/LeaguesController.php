<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Uploads;
use App\Enums\LeagueSport;
use App\Http\Controllers\Controller;
use App\Http\Resources\League as LeagueResource;
use App\Models\League;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Inertia\Inertia;

class LeaguesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request, $sport = 'all')
    {
        $keyword = $request->get('search');
        $perPage = 25;
        $query  = League::query()->with(['games']);
        if (!empty($keyword)) {
            $query->where('name', 'LIKE', "%$keyword%")
                ->orWhere('description', 'LIKE', "%$keyword%");
        }
        if ($sport && $sport != 'all') {
            $query->where('sport', $sport);
        }
        $leaguesItems = $query->latest()->paginate($perPage);
        $leagues = LeagueResource::collection($leaguesItems);
        return Inertia::render('Admin/Leagues/Index', [
            'leagues' => $leagues,
            'sport' => fn () => strlen($sport) == 3 ? strtoupper($sport) :  ucfirst($sport),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function create(Request $request, $sport = 'all')
    {
        return Inertia::render('Admin/Leagues/Create', [
            'sport' => $sport == 'all' ? null : $sport,
            'leagueSports' => collect(LeagueSport::cases())->map(fn (LeagueSport $l) => [
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
            'season' => ['required', 'string'],
            'sport' => ['required', 'string', new Enum(LeagueSport::class)],
            'image_uri' => ['required', 'string'],
            'image_upload' => ['required', 'boolean'],
            'image_path' => ['nullable', 'array'],
        ]);
        $league = new League;
        $league->leagueId = null;
        $league->sport =  $request->sport;
        $league->country =  $request->country;
        $league->season =  $request->season;
        $league->name = $request->name;
        $league->description = $request->description;
        $league->save();
        $upload = app(Uploads::class)->upload($request,  $league, 'image');
        $league->image =  $upload->url;
        $league->save();
        return redirect()->route('admin.leagues.index')->with('success', 'League added!');
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request, League $league)
    {
        $league->load(['games']);
        return Inertia::render('Admin/Leagues/Show', [
            'league' => new LeagueResource($league),
            'leagueSports' => collect(LeagueSport::cases())->map(fn (LeagueSport $l) => [
                'label' => strlen($l->value) == 3 ? strtoupper($l->value) :  ucfirst($l->value),
                'value' => $l->value,
                'key' => $l->name
            ])
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
        return Inertia::render('Admin/Leagues/Edit', [
            'league' => new LeagueResource($league),
            'leagueSports' => collect(LeagueSport::cases())->map(fn (LeagueSport $l) => [
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
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, League $league)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
            'country' => ['required', 'string'],
            'season' => ['required', 'string'],
            'sport' => ['required', 'string', new Enum(LeagueSport::class)],
            'image_uri' => ['required', 'string'],
            'image_upload' => ['required', 'boolean'],
            'image_path' => ['nullable', 'array'],
        ]);
        $upload = app(Uploads::class)->upload($request,  $league, 'image');
        $league->sport =  $request->sport;
        $league->country =  $request->country;
        $league->season =  $request->season;
        $league->name = $request->name;
        $league->description = $request->description;
        if (isset($upload->url))
            $league->image =  $upload->url;
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
        return redirect()->route('admin.leagues.index')->with('success', 'League deleted!');
    }
}
