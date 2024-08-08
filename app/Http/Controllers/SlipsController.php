<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Resources\Slip as SlipResource ;
use App\Models\Slip;
use Inertia\Inertia;

use Illuminate\Http\Request;

class SlipsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request )
    {
        $keyword = $request->get('search');
        $perPage = 25;
        $query  = Slip::query()->with(['user','stakes']);
        if (!empty($keyword)) {
            $query->where('user_id', 'LIKE', "%$keyword%")
			->orWhere('uid', 'LIKE', "%$keyword%")
			->orWhere('amount', 'LIKE', "%$keyword%")
			->orWhere('payout', 'LIKE', "%$keyword%")
			->orWhere('total_odds', 'LIKE', "%$keyword%");
        } 
        $slipsItems = $query->latest()->paginate($perPage);
        $slips = SlipResource::collection($slipsItems);
        return Inertia::render('AdminSlips/Index', compact('slips'));
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return Inertia::render('AdminSlips/Create');
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request )
    {
        $request->validate([
			'user_id' => ['required','integer','exists:users,id'],
			'amount' => ['required','numeric'],
			'payout' => ['required','numeric'],
			'total_odds' => ['required','decimal'],
		]);
        $slip = new Slip;
        $slip->user_id = $request->user_id;
		$slip->uid = $request->uid;
		$slip->amount = $request->amount;
		$slip->payout = $request->payout;
		$slip->total_odds = $request->total_odds;
		$slip->save();
        
        return redirect()->route('slips.index')->with('success', 'Slip added!');
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request, Slip $slip)
    {
        $slip->load(['user','stakes']);
        return Inertia::render('AdminSlips/Show', [
            'slip'=> new SlipResource($slip)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, Slip $slip)
    {
        $slip->load(['user','stakes']);
        return Inertia::render('AdminSlips/Edit', [
            'slip'=> new SlipResource($slip)
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
    public function update(Request $request, Slip $slip)
    {
        $request->validate([
			'user_id' => ['required','integer','exists:users,id'],
			'amount' => ['required','numeric'],
			'payout' => ['required','numeric'],
			'total_odds' => ['required','decimal'],
		]);
        
        $slip->user_id = $request->user_id;
		$slip->uid = $request->uid;
		$slip->amount = $request->amount;
		$slip->payout = $request->payout;
		$slip->total_odds = $request->total_odds;
		$slip->save();
        return back()->with('success', 'Slip updated!');
    }

     /**
     * toggle status of  the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function toggle(Request $request, Slip $slip)
    {
        $slip->active = !$slip->active;
        $slip->save();
        return back()->with('success', $slip->active ? __(' :name Slip Enabled !', ['name' => $slip->name]) : __(' :name  Slip Disabled!', ['name' => $slip->name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Request $request, Slip $slip)
    {
        $slip->delete();
        return redirect()->route('slips.index')->with('success', 'Slip deleted!');
    }
}
