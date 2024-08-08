<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AccountType;
use App\Http\Controllers\Controller;
use App\Http\Resources\Account as AccountResource;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Inertia\Inertia;

class AccountsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 25;
        $query  = Account::query()->with(['accountable']);
        if (!empty($keyword)) {
            $query->where('accountable', 'LIKE', "%$keyword%")
                ->orWhere('type', 'LIKE', "%$keyword%")
                ->orWhere('amount', 'LIKE', "%$keyword%");
        }
        $accountsItems = $query->latest()->paginate($perPage);
        $accounts = AccountResource::collection($accountsItems);
        return Inertia::render('AdminAccounts/Index', compact('accounts'));
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return Inertia::render('AdminAccounts/Create');
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => ['required', 'string', new Enum(AccountType::class)],
            'amount' => ['required', 'numeric'],
        ]);
        $account = new Account;
        $account->accountable = $request->accountable;
        $account->type = $request->type;
        $account->amount = $request->amount;
        $account->save();

        return redirect()->route('accounts.index')->with('success', 'Account added!');
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request, Account $account)
    {
        $account->load(['accountable']);
        return Inertia::render('AdminAccounts/Show', [
            'account' => new AccountResource($account)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, Account $account)
    {
        $account->load(['accountable']);
        return Inertia::render('AdminAccounts/Edit', [
            'account' => new AccountResource($account)
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
    public function update(Request $request, Account $account)
    {
        $request->validate([
            'type' => ['required', 'string', new Enum(AccountType::class)],
            'amount' => ['required', 'numeric'],
        ]);

        $account->accountable = $request->accountable;
        $account->type = $request->type;
        $account->amount = $request->amount;
        $account->save();
        return back()->with('success', 'Account updated!');
    }

    /**
     * toggle status of  the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function toggle(Request $request, Account $account)
    {
        $account->active = !$account->active;
        $account->save();
        return back()->with('success', $account->active ? __(' :name Account Enabled !', ['name' => $account->name]) : __(' :name  Account Disabled!', ['name' => $account->name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Request $request, Account $account)
    {
        $account->delete();
        return redirect()->route('accounts.index')->with('success', 'Account deleted!');
    }
}
