<?php
namespace App\Http\Controllers;
use App\Enums\StakeStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\Stake as StakeResource ;
use App\Models\Stake;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StakesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request )
    {
        $keyword = $request->get('search');
        $perPage = 25;
        $query  = Stake::query()->with(['user','game','bet','maker_trades','taker_trades']);
        if (!empty($keyword)) {
            $query->where('slip_id', 'LIKE', "%$keyword%")
			->orWhere('user_id', 'LIKE', "%$keyword%")
			->orWhere('bet_id', 'LIKE', "%$keyword%")
			->orWhere('game_id', 'LIKE', "%$keyword%")
			->orWhere('uuid', 'LIKE', "%$keyword%")
			->orWhere('scoreType', 'LIKE', "%$keyword%")
			->orWhere('amount', 'LIKE', "%$keyword%")
			->orWhere('filled', 'LIKE', "%$keyword%")
			->orWhere('unfilled', 'LIKE', "%$keyword%")
			->orWhere('payout', 'LIKE', "%$keyword%")
			->orWhere('odds', 'LIKE', "%$keyword%")
			->orWhere('status', 'LIKE', "%$keyword%")
			->orWhere('won', 'LIKE', "%$keyword%")
			->orWhere('is_withdrawn', 'LIKE', "%$keyword%")
			->orWhere('allow_partial', 'LIKE', "%$keyword%");
        } 
        $stakesItems = $query->latest()->paginate($perPage);
        $stakes = StakeResource::collection($stakesItems);
        return Inertia::render('AdminStakes/Index', compact('stakes'));
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return Inertia::render('AdminStakes/Create');
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request )
    {
        $request->validate([
			'slip_id' => ['required','integer','exists:slips,id'],
			'user_id' => ['required','integer','exists:users,id'],
			'bet_id' => ['required','integer','exists:bets,id'],
			'game_id' => ['required','integer','exists:bets,id'],
			'scoreType' => ['required','string'],
			'amount' => ['required','numeric'],
			'filled' => ['required','numeric'],
			'unfilled' => ['required','numeric'],
			'payout' => ['required','numeric'],
			'odds' => ['required','decimal'],
		]);
        $stake = new Stake;
        $stake->slip_id = $request->slip_id;
		$stake->user_id = $request->user_id;
		$stake->bet_id = $request->bet_id;
		$stake->game_id = $request->game_id;
		$stake->uuid = $request->uuid;
		$stake->scoreType = $request->scoreType;
		$stake->amount = $request->amount;
		$stake->filled = $request->filled;
		$stake->unfilled = $request->unfilled;
		$stake->payout = $request->payout;
		$stake->odds = $request->odds;
		$stake->status = $request->status;
		$stake->won = $request->won;
		$stake->is_withdrawn = $request->is_withdrawn;
		$stake->allow_partial = $request->allow_partial;
		$stake->save();
        
        return redirect()->route('stakes.index')->with('success', 'Stake added!');
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request, Stake $stake)
    {
        $stake->load(['user','game','bet','maker_trades','taker_trades']);
        return Inertia::render('AdminStakes/Show', [
            'stake'=> new StakeResource($stake)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, Stake $stake)
    {
        $stake->load(['user','game','bet','maker_trades','taker_trades']);
        return Inertia::render('AdminStakes/Edit', [
            'stake'=> new StakeResource($stake)
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
    public function update(Request $request, Stake $stake)
    {
        $request->validate([
			'slip_id' => ['required','integer','exists:slips,id'],
			'user_id' => ['required','integer','exists:users,id'],
			'bet_id' => ['required','integer','exists:bets,id'],
			'game_id' => ['required','integer','exists:bets,id'],
			'scoreType' => ['required','string'],
			'amount' => ['required','numeric'],
			'filled' => ['required','numeric'],
			'unfilled' => ['required','numeric'],
			'payout' => ['required','numeric'],
			'odds' => ['required','decimal'],
		]);
        
        $stake->slip_id = $request->slip_id;
		$stake->user_id = $request->user_id;
		$stake->bet_id = $request->bet_id;
		$stake->game_id = $request->game_id;
		$stake->uuid = $request->uuid;
		$stake->scoreType = $request->scoreType;
		$stake->amount = $request->amount;
		$stake->filled = $request->filled;
		$stake->unfilled = $request->unfilled;
		$stake->payout = $request->payout;
		$stake->odds = $request->odds;
		$stake->status = $request->status;
		$stake->won = $request->won;
		$stake->is_withdrawn = $request->is_withdrawn;
		$stake->allow_partial = $request->allow_partial;
		$stake->save();
        return back()->with('success', 'Stake updated!');
    }

     /**
     * toggle status of  the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function toggle(Request $request, Stake $stake)
    {
        $stake->active = !$stake->active;
        $stake->save();
        return back()->with('success', $stake->active ? __(' :name Stake Enabled !', ['name' => $stake->name]) : __(' :name  Stake Disabled!', ['name' => $stake->name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Request $request, Stake $stake)
    {
        $stake->delete();
        return redirect()->route('stakes.index')->with('success', 'Stake deleted!');
    }
}
