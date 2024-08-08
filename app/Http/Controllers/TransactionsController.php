<?php
namespace App\Http\Controllers;
use App\Enums\TransactionAction;
use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Http\Resources\Transaction as TransactionResource ;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TransactionsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request )
    {
        $keyword = $request->get('search');
        $perPage = 25;
        $query  = Transaction::query()->with(['user','transactable']);
        if (!empty($keyword)) {
            $query->where('user_id', 'LIKE', "%$keyword%")
			->orWhere('transactable', 'LIKE', "%$keyword%")
			->orWhere('uuid', 'LIKE', "%$keyword%")
			->orWhere('description', 'LIKE', "%$keyword%")
			->orWhere('amount', 'LIKE', "%$keyword%")
			->orWhere('balance_before', 'LIKE', "%$keyword%")
			->orWhere('action', 'LIKE', "%$keyword%")
			->orWhere('type', 'LIKE', "%$keyword%");
        } 
        $transactionsItems = $query->latest()->paginate($perPage);
        $transactions = TransactionResource::collection($transactionsItems);
        return Inertia::render('AdminTransactions/Index', compact('transactions'));
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return Inertia::render('AdminTransactions/Create');
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request )
    {
        
        $transaction = new Transaction;
        $transaction->user_id = $request->user_id;
		$transaction->transactable = $request->transactable;
		$transaction->uuid = $request->uuid;
		$transaction->description = $request->description;
		$transaction->amount = $request->amount;
		$transaction->balance_before = $request->balance_before;
		$transaction->action = $request->action;
		$transaction->type = $request->type;
		$transaction->save();
        
        return redirect()->route('transactions.index')->with('success', 'Transaction added!');
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request, Transaction $transaction)
    {
        $transaction->load(['user','transactable']);
        return Inertia::render('AdminTransactions/Show', [
            'transaction'=> new TransactionResource($transaction)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, Transaction $transaction)
    {
        $transaction->load(['user','transactable']);
        return Inertia::render('AdminTransactions/Edit', [
            'transaction'=> new TransactionResource($transaction)
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
    public function update(Request $request, Transaction $transaction)
    {
        
        
        $transaction->user_id = $request->user_id;
		$transaction->transactable = $request->transactable;
		$transaction->uuid = $request->uuid;
		$transaction->description = $request->description;
		$transaction->amount = $request->amount;
		$transaction->balance_before = $request->balance_before;
		$transaction->action = $request->action;
		$transaction->type = $request->type;
		$transaction->save();
        return back()->with('success', 'Transaction updated!');
    }

     /**
     * toggle status of  the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function toggle(Request $request, Transaction $transaction)
    {
        $transaction->active = !$transaction->active;
        $transaction->save();
        return back()->with('success', $transaction->active ? __(' :name Transaction Enabled !', ['name' => $transaction->name]) : __(' :name  Transaction Disabled!', ['name' => $transaction->name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Request $request, Transaction $transaction)
    {
        $transaction->delete();
        return redirect()->route('transactions.index')->with('success', 'Transaction deleted!');
    }
}
