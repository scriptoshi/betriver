<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Resources\Wager as WagerResource ;
use App\Models\Wager;
use Inertia\Inertia;

use Illuminate\Http\Request;

class WagersController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request )
    {
        $keyword = $request->get('search');
        $perPage = 25;
        $query  = Wager::query()->with(['bet','ticket','game','odd']);
        if (!empty($keyword)) {
            $query->where('ticket_id', 'LIKE', "%$keyword%")
			->orWhere('bet_id', 'LIKE', "%$keyword%")
			->orWhere('game_id', 'LIKE', "%$keyword%")
			->orWhere('odd_id', 'LIKE', "%$keyword%")
			->orWhere('scoreType', 'LIKE', "%$keyword%")
			->orWhere('odds', 'LIKE', "%$keyword%")
			->orWhere('winner', 'LIKE', "%$keyword%");
        } 
        $wagersItems = $query->latest()->paginate($perPage);
        $wagers = WagerResource::collection($wagersItems);
        return Inertia::render('AdminWagers/Index', compact('wagers'));
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return Inertia::render('AdminWagers/Create');
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request )
    {
        $request->validate([
			'ticket_id' => ['required','integer','exists:tickets,id'],
			'bet_id' => ['required','integer','exists:bets,id'],
			'game_id' => ['required','integer','exists:games,id'],
			'odd_id' => ['required','integer','exists:tickets,id'],
			'scoreType' => ['required','string'],
			'odds' => ['required','decimal'],
		]);
        $wager = new Wager;
        $wager->ticket_id = $request->ticket_id;
		$wager->bet_id = $request->bet_id;
		$wager->game_id = $request->game_id;
		$wager->odd_id = $request->odd_id;
		$wager->scoreType = $request->scoreType;
		$wager->odds = $request->odds;
		$wager->winner = $request->winner;
		$wager->save();
        
        return redirect()->route('wagers.index')->with('success', 'Wager added!');
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request, Wager $wager)
    {
        $wager->load(['bet','ticket','game','odd']);
        return Inertia::render('AdminWagers/Show', [
            'wager'=> new WagerResource($wager)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, Wager $wager)
    {
        $wager->load(['bet','ticket','game','odd']);
        return Inertia::render('AdminWagers/Edit', [
            'wager'=> new WagerResource($wager)
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
    public function update(Request $request, Wager $wager)
    {
        $request->validate([
			'ticket_id' => ['required','integer','exists:tickets,id'],
			'bet_id' => ['required','integer','exists:bets,id'],
			'game_id' => ['required','integer','exists:games,id'],
			'odd_id' => ['required','integer','exists:tickets,id'],
			'scoreType' => ['required','string'],
			'odds' => ['required','decimal'],
		]);
        
        $wager->ticket_id = $request->ticket_id;
		$wager->bet_id = $request->bet_id;
		$wager->game_id = $request->game_id;
		$wager->odd_id = $request->odd_id;
		$wager->scoreType = $request->scoreType;
		$wager->odds = $request->odds;
		$wager->winner = $request->winner;
		$wager->save();
        return back()->with('success', 'Wager updated!');
    }

     /**
     * toggle status of  the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function toggle(Request $request, Wager $wager)
    {
        $wager->active = !$wager->active;
        $wager->save();
        return back()->with('success', $wager->active ? __(' :name Wager Enabled !', ['name' => $wager->name]) : __(' :name  Wager Disabled!', ['name' => $wager->name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Request $request, Wager $wager)
    {
        $wager->delete();
        return redirect()->route('wagers.index')->with('success', 'Wager deleted!');
    }
}
