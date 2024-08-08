<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CommissionType;
use App\Http\Controllers\Controller;
use App\Http\Resources\Commission as CommissionResource;
use App\Models\Commission;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Inertia\Inertia;

class CommissionsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request, $type = CommissionType::DEPOSIT)
    {
        $commissionsItems  = Commission::query()
            ->with(['payouts'])
            ->where('type', $type)
            ->latest()
            ->get();
        $commissions = CommissionResource::collection($commissionsItems);
        return Inertia::render('Admin/Commissions/Index', compact('commissions', 'type'));
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function create(Request $request, CommissionType $type, $num = 5)
    {
        $commissions = Commission::query()->where('type', $type)->pluck('percent', 'level')->all();
        return Inertia::render('Admin/Commissions/Create', [
            'commissions' => $commissions,
            'type' => $type,
            'generate' => $num
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => ['required', 'string', new Enum(CommissionType::class)],
            'commissions' => ['array'],
            'commissions.*.level' => ['required', 'integer'],
            'commissions.*.percent' => ['required', 'numeric']
        ]);
        Commission::where('type', $request->type)->delete();
        foreach ($request->commissions as $comm) {
            $commission = new Commission;
            $commission->type = $request->type;
            $commission->level = $comm['level'];
            $commission->percent =  $comm['percent'];
            $commission->active = true;
            $commission->save();
        }
        return redirect()->route('admin.commissions.index')->with('success', 'Commission rebuilt!');
    }
    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request, Commission $commission)
    {
        $commission->load(['payouts']);
        return Inertia::render('Admin/Commissions/Show', [
            'commission' => new CommissionResource($commission)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(Request $request,  Commission $commission)
    {
        $commission->load(['payouts']);
        return Inertia::render('Admin/Commissions/Edit', [
            'commission' => new CommissionResource($commission)
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
    public function update(Request $request, Commission $commission)
    {
        $request->validate([
            'type' => ['required', 'string', new Enum(CommissionType::class)],
            'level' => ['required', 'integer'],
            'percent' => ['required', 'numeric'],
            'active' => ['required', 'boolean'],
        ]);

        $commission->type = $request->type;
        $commission->level = $request->level;
        $commission->percent = $request->percent;
        $commission->active = $request->active;
        $commission->save();
        return back()->with('success', 'Commission updated!');
    }

    /**
     * toggle status of  the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function toggle(Request $request, Commission $commission)
    {
        $commission->active = !$commission->active;
        $commission->save();
        return back()->with('success', $commission->active ? __(' :name Commission Enabled !', ['name' => $commission->name]) : __(' :name  Commission Disabled!', ['name' => $commission->name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Request $request, Commission $commission)
    {
        $commission->delete();
        return redirect()->route('commissions.index')->with('success', 'Commission deleted!');
    }
}
