<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Resources\Odd as OddResource ;
use App\Models\Odd;
use Inertia\Inertia;

use Illuminate\Http\Request;

class OddsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request )
    {
        $keyword = $request->get('search');
        $perPage = 25;
        $query  = Odd::query()->with(['bet','game']);
        if (!empty($keyword)) {
            $query->where('bet_id', 'LIKE', "%$keyword%")
			->orWhere('game_id', 'LIKE', "%$keyword%")
			->orWhere('odd', 'LIKE', "%$keyword%");
        } 
        $oddsItems = $query->latest()->paginate($perPage);
        $odds = OddResource::collection($oddsItems);
        return Inertia::render('AdminOdds/Index', compact('odds'));
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return Inertia::render('AdminOdds/Create');
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request )
    {
        $request->validate([
			'bet_id' => ['required','integer','exists:bets,id'],
			'game_id' => ['required','integer','exists:games,id'],
			'odd' => ['required','numeric'],
		]);
        $odd = new Odd;
        $odd->bet_id = $request->bet_id;
		$odd->game_id = $request->game_id;
		$odd->odd = $request->odd;
		$odd->save();
        
        return redirect()->route('odds.index')->with('success', 'Odd added!');
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request, Odd $odd)
    {
        $odd->load(['bet','game']);
        return Inertia::render('AdminOdds/Show', [
            'odd'=> new OddResource($odd)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, Odd $odd)
    {
        $odd->load(['bet','game']);
        return Inertia::render('AdminOdds/Edit', [
            'odd'=> new OddResource($odd)
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
    public function update(Request $request, Odd $odd)
    {
        $request->validate([
			'bet_id' => ['required','integer','exists:bets,id'],
			'game_id' => ['required','integer','exists:games,id'],
			'odd' => ['required','numeric'],
		]);
        
        $odd->bet_id = $request->bet_id;
		$odd->game_id = $request->game_id;
		$odd->odd = $request->odd;
		$odd->save();
        return back()->with('success', 'Odd updated!');
    }

     /**
     * toggle status of  the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function toggle(Request $request, Odd $odd)
    {
        $odd->active = !$odd->active;
        $odd->save();
        return back()->with('success', $odd->active ? __(' :name Odd Enabled !', ['name' => $odd->name]) : __(' :name  Odd Disabled!', ['name' => $odd->name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Request $request, Odd $odd)
    {
        $odd->delete();
        return redirect()->route('odds.index')->with('success', 'Odd deleted!');
    }
}
