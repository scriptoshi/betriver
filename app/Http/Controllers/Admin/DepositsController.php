<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DepositStatus;
use App\Gateways\Payment\Actions\DepositTx;
use App\Http\Controllers\Controller;
use App\Http\Resources\Deposit as DepositResource;

use App\Models\Deposit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DepositsController extends Controller
{


    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 25;
        $query  = Deposit::with(['user']);
        if (!empty($keyword)) {
            $query->where('uuid', 'LIKE', "%$keyword%")
                ->orWhere('gateway', 'LIKE', "%$keyword%")
                ->orWhere('amount', 'LIKE', "%$keyword%")
                ->orWhere('data', 'LIKE', "%$keyword%");
        }
        $depositsItems = $query->latest()->paginate($perPage);
        $deposits = DepositResource::collection($depositsItems);
        return Inertia::render('Admin/Deposits/Index', [
            'deposits' => $deposits
        ]);
    }



    /**
     * force fail a deposit.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function fail(Request $request, Deposit $deposit)
    {
        $deposit->status = DepositStatus::FAILED;
        $deposit->gateway_error = __('Deposit cancelled by admin');
        $deposit->save();
        return  back();
    }

    /**
     * force complete/ approve a deposit.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function complete(Request $request, Deposit $deposit)
    {
        if ($deposit->status == DepositStatus::COMPLETE) {
            return back()->with('error', "Deposit already complete");
        }
        $deposit->status = DepositStatus::COMPLETE;
        $deposit->gateway_error = null;
        $deposit->save();
        app(DepositTx::class)->create($deposit);
        return  back();
    }
}
