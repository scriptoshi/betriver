<?php
namespace App\Http\Controllers;
use App\Enums\DepositGateway;
use App\Enums\DepositStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\Deposit as DepositResource ;
use App\Models\Deposit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DepositsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request )
    {
        $keyword = $request->get('search');
        $perPage = 25;
        $query  = Deposit::query()->with(['user','transaction']);
        if (!empty($keyword)) {
            $query->where('user_id', 'LIKE', "%$keyword%")
			->orWhere('uuid', 'LIKE', "%$keyword%")
			->orWhere('gateway', 'LIKE', "%$keyword%")
			->orWhere('remoteId', 'LIKE', "%$keyword%")
			->orWhere('from', 'LIKE', "%$keyword%")
			->orWhere('gross_amount', 'LIKE', "%$keyword%")
			->orWhere('fees', 'LIKE', "%$keyword%")
			->orWhere('amount', 'LIKE', "%$keyword%")
			->orWhere('data', 'LIKE', "%$keyword%")
			->orWhere('status', 'LIKE', "%$keyword%");
        } 
        $depositsItems = $query->latest()->paginate($perPage);
        $deposits = DepositResource::collection($depositsItems);
        return Inertia::render('AdminDeposits/Index', compact('deposits'));
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return Inertia::render('AdminDeposits/Create');
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request )
    {
        
        $deposit = new Deposit;
        $deposit->user_id = $request->user_id;
		$deposit->uuid = $request->uuid;
		$deposit->gateway = $request->gateway;
		$deposit->remoteId = $request->remoteId;
		$deposit->from = $request->from;
		$deposit->gross_amount = $request->gross_amount;
		$deposit->fees = $request->fees;
		$deposit->amount = $request->amount;
		$deposit->data = $request->data;
		$deposit->status = $request->status;
		$deposit->save();
        
        return redirect()->route('deposits.index')->with('success', 'Deposit added!');
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request, Deposit $deposit)
    {
        $deposit->load(['user','transaction']);
        return Inertia::render('AdminDeposits/Show', [
            'deposit'=> new DepositResource($deposit)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, Deposit $deposit)
    {
        $deposit->load(['user','transaction']);
        return Inertia::render('AdminDeposits/Edit', [
            'deposit'=> new DepositResource($deposit)
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
    public function update(Request $request, Deposit $deposit)
    {
        
        
        $deposit->user_id = $request->user_id;
		$deposit->uuid = $request->uuid;
		$deposit->gateway = $request->gateway;
		$deposit->remoteId = $request->remoteId;
		$deposit->from = $request->from;
		$deposit->gross_amount = $request->gross_amount;
		$deposit->fees = $request->fees;
		$deposit->amount = $request->amount;
		$deposit->data = $request->data;
		$deposit->status = $request->status;
		$deposit->save();
        return back()->with('success', 'Deposit updated!');
    }

     /**
     * toggle status of  the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function toggle(Request $request, Deposit $deposit)
    {
        $deposit->active = !$deposit->active;
        $deposit->save();
        return back()->with('success', $deposit->active ? __(' :name Deposit Enabled !', ['name' => $deposit->name]) : __(' :name  Deposit Disabled!', ['name' => $deposit->name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Request $request, Deposit $deposit)
    {
        $deposit->delete();
        return redirect()->route('deposits.index')->with('success', 'Deposit deleted!');
    }
}
