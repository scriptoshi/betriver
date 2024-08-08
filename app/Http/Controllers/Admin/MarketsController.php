<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Market as MarketResource;
use App\Models\Market;
use Inertia\Inertia;

use Illuminate\Http\Request;

class MarketsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request, $sport = 'all')
    {
        $keyword = $request->get('search');
        $query  = Market::query()->with(['bets', 'games']);
        if (!empty($keyword)) {
            $query->where('name', 'LIKE', "%$keyword%")
                ->orWhere('slug', 'LIKE', "%$keyword%");
        }
        if ($sport && $sport != 'all') {
            $query->where('sport', $sport);
        }
        $marketsItems = $query->latest()->get();

        return Inertia::render('Admin/Markets/Index', [
            'markets' => MarketResource::collection($marketsItems),
            'sport' => fn () => strlen($sport) == 3 ? strtoupper($sport) :  ucfirst($sport),
        ]);
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
