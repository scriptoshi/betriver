<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Trade as TradeResource;
use App\Models\Trade;
use Inertia\Inertia;

use Illuminate\Http\Request;

class TradesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 25;
        $query  = Trade::query()->with(['maker', 'taker']);
        if (!empty($keyword)) {
            $query->where('maker_id', 'LIKE', "%$keyword%")
                ->orWhere('taker_id', 'LIKE', "%$keyword%")
                ->orWhere('amount', 'LIKE', "%$keyword%")
                ->orWhere('buy', 'LIKE', "%$keyword%")
                ->orWhere('sell', 'LIKE', "%$keyword%")
                ->orWhere('margin', 'LIKE', "%$keyword%");
        }
        $tradesItems = $query->latest()->paginate($perPage);
        $trades = TradeResource::collection($tradesItems);
        return Inertia::render('AdminTrades/Index', compact('trades'));
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return Inertia::render('AdminTrades/Create');
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $request->validate([
            'maker_id' => ['required', 'integer', 'exists:stakes,id'],
            'taker_id' => ['required', 'integer', 'exists:stakes,id'],
            'amount' => ['required', 'numeric'],
            'buy' => ['required', 'numeric'],
            'sell' => ['required', 'numeric'],
            'margin' => ['required', 'numeric'],
        ]);
        $trade = new Trade;
        $trade->maker_id = $request->maker_id;
        $trade->taker_id = $request->taker_id;
        $trade->amount = $request->amount;
        $trade->buy = $request->buy;
        $trade->sell = $request->sell;
        $trade->margin = $request->margin;
        $trade->save();

        return redirect()->route('trades.index')->with('success', 'Trade added!');
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request, Trade $trade)
    {
        $trade->load(['maker', 'taker']);
        return Inertia::render('AdminTrades/Show', [
            'trade' => new TradeResource($trade)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, Trade $trade)
    {
        $trade->load(['maker', 'taker']);
        return Inertia::render('AdminTrades/Edit', [
            'trade' => new TradeResource($trade)
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
    public function update(Request $request, Trade $trade)
    {
        $request->validate([
            'maker_id' => ['required', 'integer', 'exists:stakes,id'],
            'taker_id' => ['required', 'integer', 'exists:stakes,id'],
            'amount' => ['required', 'numeric'],
            'buy' => ['required', 'numeric'],
            'sell' => ['required', 'numeric'],
            'margin' => ['required', 'numeric'],
        ]);

        $trade->maker_id = $request->maker_id;
        $trade->taker_id = $request->taker_id;
        $trade->amount = $request->amount;
        $trade->buy = $request->buy;
        $trade->sell = $request->sell;
        $trade->margin = $request->margin;
        $trade->save();
        return back()->with('success', 'Trade updated!');
    }

    /**
     * toggle status of  the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function toggle(Request $request, Trade $trade)
    {
        $trade->active = !$trade->active;
        $trade->save();
        return back()->with('success', $trade->active ? __(' :name Trade Enabled !', ['name' => $trade->name]) : __(' :name  Trade Disabled!', ['name' => $trade->name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Request $request, Trade $trade)
    {
        $trade->delete();
        return redirect()->route('trades.index')->with('success', 'Trade deleted!');
    }
}
