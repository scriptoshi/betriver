<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Resources\Payout as PayoutResource ;
use App\Models\Payout;
use Inertia\Inertia;

use Illuminate\Http\Request;

class PayoutsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request )
    {
        $keyword = $request->get('search');
        $perPage = 25;
        $query  = Payout::query()->with(['commission','user','referral','transaction']);
        if (!empty($keyword)) {
            $query->where('commission_id', 'LIKE', "%$keyword%")
			->orWhere('user_id', 'LIKE', "%$keyword%")
			->orWhere('referral_id', 'LIKE', "%$keyword%")
			->orWhere('uuid', 'LIKE', "%$keyword%")
			->orWhere('description', 'LIKE', "%$keyword%")
			->orWhere('amount', 'LIKE', "%$keyword%")
			->orWhere('percent', 'LIKE', "%$keyword%");
        } 
        $payoutsItems = $query->latest()->paginate($perPage);
        $payouts = PayoutResource::collection($payoutsItems);
        return Inertia::render('AdminPayouts/Index', compact('payouts'));
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return Inertia::render('AdminPayouts/Create');
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request )
    {
        
        $payout = new Payout;
        $payout->commission_id = $request->commission_id;
		$payout->user_id = $request->user_id;
		$payout->referral_id = $request->referral_id;
		$payout->uuid = $request->uuid;
		$payout->description = $request->description;
		$payout->amount = $request->amount;
		$payout->percent = $request->percent;
		$payout->save();
        
        return redirect()->route('payouts.index')->with('success', 'Payout added!');
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request, Payout $payout)
    {
        $payout->load(['commission','user','referral','transaction']);
        return Inertia::render('AdminPayouts/Show', [
            'payout'=> new PayoutResource($payout)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, Payout $payout)
    {
        $payout->load(['commission','user','referral','transaction']);
        return Inertia::render('AdminPayouts/Edit', [
            'payout'=> new PayoutResource($payout)
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
    public function update(Request $request, Payout $payout)
    {
        
        
        $payout->commission_id = $request->commission_id;
		$payout->user_id = $request->user_id;
		$payout->referral_id = $request->referral_id;
		$payout->uuid = $request->uuid;
		$payout->description = $request->description;
		$payout->amount = $request->amount;
		$payout->percent = $request->percent;
		$payout->save();
        return back()->with('success', 'Payout updated!');
    }

     /**
     * toggle status of  the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function toggle(Request $request, Payout $payout)
    {
        $payout->active = !$payout->active;
        $payout->save();
        return back()->with('success', $payout->active ? __(' :name Payout Enabled !', ['name' => $payout->name]) : __(' :name  Payout Disabled!', ['name' => $payout->name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Request $request, Payout $payout)
    {
        $payout->delete();
        return redirect()->route('payouts.index')->with('success', 'Payout deleted!');
    }
}
