<?php
namespace App\Http\Controllers;
use App\Enums\WithdrawGateway;
use App\Enums\WithdrawStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\Withdraw as WithdrawResource ;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WithdrawsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request )
    {
        $keyword = $request->get('search');
        $perPage = 25;
        $query  = Withdraw::query()->with(['user','transaction']);
        if (!empty($keyword)) {
            $query->where('user_id', 'LIKE', "%$keyword%")
			->orWhere('uuid', 'LIKE', "%$keyword%")
			->orWhere('gateway', 'LIKE', "%$keyword%")
			->orWhere('remoteId', 'LIKE', "%$keyword%")
			->orWhere('to', 'LIKE', "%$keyword%")
			->orWhere('gross_amount', 'LIKE', "%$keyword%")
			->orWhere('fees', 'LIKE', "%$keyword%")
			->orWhere('amount', 'LIKE', "%$keyword%")
			->orWhere('data', 'LIKE', "%$keyword%")
			->orWhere('status', 'LIKE', "%$keyword%");
        } 
        $withdrawsItems = $query->latest()->paginate($perPage);
        $withdraws = WithdrawResource::collection($withdrawsItems);
        return Inertia::render('AdminWithdraws/Index', compact('withdraws'));
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return Inertia::render('AdminWithdraws/Create');
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request )
    {
        
        $withdraw = new Withdraw;
        $withdraw->user_id = $request->user_id;
		$withdraw->uuid = $request->uuid;
		$withdraw->gateway = $request->gateway;
		$withdraw->remoteId = $request->remoteId;
		$withdraw->to = $request->to;
		$withdraw->gross_amount = $request->gross_amount;
		$withdraw->fees = $request->fees;
		$withdraw->amount = $request->amount;
		$withdraw->data = $request->data;
		$withdraw->status = $request->status;
		$withdraw->save();
        
        return redirect()->route('withdraws.index')->with('success', 'Withdraw added!');
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request, Withdraw $withdraw)
    {
        $withdraw->load(['user','transaction']);
        return Inertia::render('AdminWithdraws/Show', [
            'withdraw'=> new WithdrawResource($withdraw)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, Withdraw $withdraw)
    {
        $withdraw->load(['user','transaction']);
        return Inertia::render('AdminWithdraws/Edit', [
            'withdraw'=> new WithdrawResource($withdraw)
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
    public function update(Request $request, Withdraw $withdraw)
    {
        
        
        $withdraw->user_id = $request->user_id;
		$withdraw->uuid = $request->uuid;
		$withdraw->gateway = $request->gateway;
		$withdraw->remoteId = $request->remoteId;
		$withdraw->to = $request->to;
		$withdraw->gross_amount = $request->gross_amount;
		$withdraw->fees = $request->fees;
		$withdraw->amount = $request->amount;
		$withdraw->data = $request->data;
		$withdraw->status = $request->status;
		$withdraw->save();
        return back()->with('success', 'Withdraw updated!');
    }

     /**
     * toggle status of  the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function toggle(Request $request, Withdraw $withdraw)
    {
        $withdraw->active = !$withdraw->active;
        $withdraw->save();
        return back()->with('success', $withdraw->active ? __(' :name Withdraw Enabled !', ['name' => $withdraw->name]) : __(' :name  Withdraw Disabled!', ['name' => $withdraw->name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Request $request, Withdraw $withdraw)
    {
        $withdraw->delete();
        return redirect()->route('withdraws.index')->with('success', 'Withdraw deleted!');
    }
}
