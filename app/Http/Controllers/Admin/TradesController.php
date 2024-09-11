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
        $perPage = 25;
        $query  = Trade::query()->with(['maker', 'taker']);
        $tradesItems = $query->latest()->paginate($perPage);
        $trades = TradeResource::collection($tradesItems);
        return Inertia::render('Admin/Trades/Index', compact('trades'));
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
