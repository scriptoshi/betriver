<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Resources\Market as MarketResource ;
use App\Models\Market;
use Inertia\Inertia;

use Illuminate\Http\Request;

class MarketsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request )
    {
        $keyword = $request->get('search');
        $perPage = 25;
        $query  = Market::query()->with(['bets','games']);
        if (!empty($keyword)) {
            $query->where('name', 'LIKE', "%$keyword%")
			->orWhere('slug', 'LIKE', "%$keyword%")
			->orWhere('mode', 'LIKE', "%$keyword%");
        } 
        $marketsItems = $query->latest()->paginate($perPage);
        $markets = MarketResource::collection($marketsItems);
        return Inertia::render('AdminMarkets/Index', compact('markets'));
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return Inertia::render('AdminMarkets/Create');
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request )
    {
        
        $market = new Market;
        $market->name = $request->name;
		$market->slug = $request->slug;
		$market->mode = $request->mode;
		$market->save();
        
        return redirect()->route('markets.index')->with('success', 'Market added!');
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request, Market $market)
    {
        $market->load(['bets','games']);
        return Inertia::render('AdminMarkets/Show', [
            'market'=> new MarketResource($market)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, Market $market)
    {
        $market->load(['bets','games']);
        return Inertia::render('AdminMarkets/Edit', [
            'market'=> new MarketResource($market)
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
    public function update(Request $request, Market $market)
    {
        
        
        $market->name = $request->name;
		$market->slug = $request->slug;
		$market->mode = $request->mode;
		$market->save();
        return back()->with('success', 'Market updated!');
    }

     /**
     * toggle status of  the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function toggle(Request $request, Market $market)
    {
        $market->active = !$market->active;
        $market->save();
        return back()->with('success', $market->active ? __(' :name Market Enabled !', ['name' => $market->name]) : __(' :name  Market Disabled!', ['name' => $market->name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Request $request, Market $market)
    {
        $market->delete();
        return redirect()->route('markets.index')->with('success', 'Market deleted!');
    }
}
