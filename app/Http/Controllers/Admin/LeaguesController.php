<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Uploads;
use App\Enums\LeagueSport;
use App\Enums\RaceTag;
use App\Http\Controllers\Controller;
use App\Http\Resources\League as LeagueResource;
use App\Models\Game;
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
        $country = $request->get('country');
        $active = $request->get('active');
        $submenu = $request->get('submenu');
        $odds = $request->get('odds');
        $perPage = 25;
        $query  = League::query()->with(['games']);
        if (!empty($keyword)) {
            $query->where('name', 'LIKE', "%$keyword%")
                ->orWhere('description', 'LIKE', "%$keyword%");
        }
        if ($sport && $sport != 'all') {
            $query->where('sport', $sport);
        }
        if (!empty($country)) {
            $query->where(function ($query) use ($country) {
                $query->where('country', $country)
                    ->orWhere('country', 'LIKE', "%$country%");
                if ($country == 'GB-ENG') {
                    $query->orWhere('country', 'LIKE', "%England%");
                }
            });
        }
        if (!empty($active)) {
            $query->where('active', true);
        }
        if (!empty($submenu)) {
            $query->where('menu', true);
        }
        if (!empty($odds)) {
            $query->where('has_odds', true);
        }
        $leaguesItems = $query->latest()->paginate($perPage)->withQueryString();
        $leagues = LeagueResource::collection($leaguesItems);
        return Inertia::render('Admin/Leagues/Index', [
            'leagues' => $leagues,
            'sport' => fn() => strlen($sport) == 3 ? strtoupper($sport) :  ucfirst($sport),
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
            'racetags' =>  collect(RaceTag::cases())->map(fn(RaceTag $l) => [
                'label' => $l->name(),
                'value' => $l->value,
                'key' => $l->name
            ]),
            'leagueSports' => collect(LeagueSport::cases())->map(fn(LeagueSport $l) => [
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
            'race_tag' => ['nullable', 'required_if:sport,racing', 'string', new Enum(RaceTag::class)],
            'image_uri' => ['required', 'string'],
            'image_upload' => ['required', 'boolean'],
            'image_path' => ['nullable', 'array'],
        ]);
        $league = new League;
        $league->leagueId = null;
        $league->sport =  $request->sport;
        $league->country =  $request->country;
        $league->season =  $request->season;
        $league->race_tag =  $request->race_tag;
        $league->name = $request->name;
        $league->description = $request->description;
        $league->save();
        $upload = app(Uploads::class)->upload($request,  $league, 'image');
        $league->image =  $upload->url;
        $league->save();
        return redirect()->route('admin.leagues.index')->with('success', 'League added!');
    }


    /**
     * Pull leagues from API
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function pull(Request $request)
    {
        $request->validate([
            'sport' => ['required', 'string', new Enum(LeagueSport::class)]
        ]);
        LeagueSport::from($request->sport)->api()::updateLeagues();
        // cleare menu cache
        League::clearCache();
        Game::clearCache();
        return back()->with('success', 'Leagues Loaded');
    }

    /**
     * toggle status of  the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function loadOddsFromApi(Request $request, League $league)
    {
        $apiKey = config('services.apifootball.apikey', settings('site.apifootball_api_key'));
        if (empty($apiKey)) return back()->with('error', 'Missing api key');
        return $league->sport->api()::loadOdds($league);
        // cleare menu cache
        League::clearCache();
        Game::clearCache();
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
            'sport' => fn() => strlen($league->sport->value) == 3 ? strtoupper($league->sport->value) :  ucfirst($league->sport->value),
            'leagueSports' => collect(LeagueSport::cases())->map(fn(LeagueSport $l) => [
                'label' => strlen($l->value) == 3 ? strtoupper($l->value) :  ucfirst($l->value),
                'value' => $l->value,
                'key' => $l->name
            ]),
            'racetags' =>  collect(RaceTag::cases())->map(fn(RaceTag $l) => [
                'label' => $l->name(),
                'value' => $l->value,
                'key' => $l->name
            ]),
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
            'race_tag' => ['nullable', 'required_if:sport,racing', 'string', new Enum(RaceTag::class)],
            'sport' => ['required', 'string', new Enum(LeagueSport::class)],
            'image_uri' => ['required', 'string'],
            'image_upload' => ['required', 'boolean'],
            'image_path' => ['nullable', 'array'],
        ]);
        $upload = app(Uploads::class)->upload($request,  $league, 'image');
        $league->sport =  $request->sport;
        $league->country =  $request->country;
        $league->season =  $request->season;
        $league->race_tag =  $request->race_tag;
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
     * toggle status of  the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function toggleMenu(Request $request, League $league)
    {
        $league->menu = !$league->menu;
        $league->save();
        return back()->with('success', $league->menu ? __(' :name League Submenu Added!', ['name' => $league->name]) : __(' :name  League Submenu Removed!', ['name' => $league->name]));
    }

    /**
     * diable all the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function disableAll(Request $request)
    {
        $query = League::query();
        if ($request->filled('country')) {
            $query->where('country', $request->country);
        }
        if ($request->filled('sport')) {
            $query->where('sport', $request->sport);
        }
        $query->update(['active' => FALSE]);

        return back()->with('success', __('All leagues disabled'));
    }

    /**
     * diable all the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function enableAll(Request $request)
    {
        $query = League::query();
        if ($request->filled('country')) {
            $query->where('country', $request->country);
        }
        if ($request->filled('sport')) {
            $query->where('sport', $request->sport);
        }
        $query->update(['active' => true]);
        return back()->with('success', __('All leagues enabled'));
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
