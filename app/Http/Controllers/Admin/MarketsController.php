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
        $marketsItems = $query->oldest('sequence')->latest()->get();

        return Inertia::render('Admin/Markets/Index', [
            'markets' => MarketResource::collection($marketsItems),
            'sport' => fn() => strlen($sport) == 3 ? strtoupper($sport) :  ucfirst($sport),
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
     * Reorder markets based on provided sequence numbers.
     *
     * This method updates the sequence of market models in the database.
     * It expects an array of market data, where each item contains an ID
     * and a new sequence number for that market.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     *
     * @example
     * $request->markets = [
     *     ['id' => 1, 'sequence' => 3],
     *     ['id' => 2, 'sequence' => 1],
     *     ['id' => 3, 'sequence' => 2]
     * ];
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'markets' => 'required|array',
            'markets.*.id' => 'required|exists:markets,id',
            'markets.*.sequence' => 'required|integer|min:0',
        ]);

        $markets = collect($request->input('markets'));

        // Using updateOrCreate to handle the reordering
        foreach ($markets as $item) {
            Market::where('id', $item['id'])->update(['sequence' => $item['sequence']]);
        }

        return back()->with('success', 'Markets reordered successfully');
    }

    /** 
     * Update the sequence of a single market or all markets.
     *
     * This method updates the sequence number of either a specific market
     * or all markets in the database, based on the provided parameters.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     *
     * @example
     * // Update a single market
     * $request->all() = [
     *     'setAll' => false,
     *     'market_id' => 5,
     *     'sequence' => 3
     * ];
     * 
     * // Update all markets
     * $request->all() = [
     *     'setAll' => true,
     *     'sequence' => 10
     * ];
     */
    public function sequence(Request $request)
    {
        $request->validate([
            'setAll' => 'required|boolean',
            'market_id' => 'required_if:setAll,false|exists:markets,id',
            'sequence' => 'required|integer|min:0',
        ]);

        if ($request->setAll) {
            $market = Market::find($request->market_id);
            // Update all markets
            Market::query()
                ->where('sport', $market->sport)
                ->update(['sequence' => $request->sequence]);
            $message = 'All markets updated to sequence ' . $request->sequence;
        } else {
            // Update a single market
            Market::where('id', $request->market_id)->update(['sequence' => $request->sequence]);
            $message = 'Market ' . $request->market_id . ' updated to sequence ' . $request->sequence;
        }

        return back()->with('success', $message);
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
