<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BetMode;
use App\Enums\GoalCount;
use App\Enums\Halfs;
use App\Http\Controllers\Controller;
use App\Http\Resources\Bet as BetResource;
use App\Models\Bet;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BetsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 25;
        $query  = Bet::query()->with(['market', 'odds', 'stakes', 'tickets', 'wagers', 'winGames']);
        if (!empty($keyword)) {
            $query->where('market_id', 'LIKE', "%$keyword%")
                ->orWhere('betId', 'LIKE', "%$keyword%")
                ->orWhere('name', 'LIKE', "%$keyword%")
                ->orWhere('boolOutcome', 'LIKE', "%$keyword%")
                ->orWhere('mode', 'LIKE', "%$keyword%")
                ->orWhere('half', 'LIKE', "%$keyword%")
                ->orWhere('team', 'LIKE', "%$keyword%")
                ->orWhere('result', 'LIKE', "%$keyword%")
                ->orWhere('is_compound_bet', 'LIKE', "%$keyword%");
        }
        $betsItems = $query->latest()->paginate($perPage);
        $bets = BetResource::collection($betsItems);
        return Inertia::render('AdminBets/Index', compact('bets'));
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return Inertia::render('AdminBets/Create');
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $request->validate([
            'result' => ['required', 'numeric'],
        ]);
        $bet = new Bet;
        $bet->market_id = $request->market_id;
        $bet->betId = $request->betId;
        $bet->name = $request->name;
        $bet->boolOutcome = $request->boolOutcome;
        $bet->mode = $request->mode;
        $bet->half = $request->half;
        $bet->team = $request->team;
        $bet->result = $request->result;
        $bet->is_compound_bet = $request->is_compound_bet;
        $bet->save();

        return redirect()->route('bets.index')->with('success', 'Bet added!');
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request, Bet $bet)
    {
        $bet->load(['market', 'odds', 'stakes', 'tickets', 'wagers', 'winGames']);
        return Inertia::render('AdminBets/Show', [
            'bet' => new BetResource($bet)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, Bet $bet)
    {
        $bet->load(['market', 'odds', 'stakes', 'tickets', 'wagers', 'winGames']);
        return Inertia::render('AdminBets/Edit', [
            'bet' => new BetResource($bet)
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
    public function update(Request $request, Bet $bet)
    {
        $request->validate([
            'result' => ['required', 'numeric'],
        ]);

        $bet->market_id = $request->market_id;
        $bet->betId = $request->betId;
        $bet->name = $request->name;
        $bet->boolOutcome = $request->boolOutcome;
        $bet->mode = $request->mode;
        $bet->half = $request->half;
        $bet->team = $request->team;
        $bet->result = $request->result;
        $bet->is_compound_bet = $request->is_compound_bet;
        $bet->save();
        return back()->with('success', 'Bet updated!');
    }

    /**
     * toggle status of  the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function toggle(Request $request, Bet $bet)
    {
        $bet->active = !$bet->active;
        $bet->save();
        return back()->with('success', $bet->active ? __(' :name Bet Enabled !', ['name' => $bet->name]) : __(' :name  Bet Disabled!', ['name' => $bet->name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Request $request, Bet $bet)
    {
        $bet->delete();
        return redirect()->route('bets.index')->with('success', 'Bet deleted!');
    }
}
