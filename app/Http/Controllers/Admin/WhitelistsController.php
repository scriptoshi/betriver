<?php

namespace App\Http\Controllers\Admin;

use App\Enums\WhitelistStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\Whitelist as WhitelistResource;
use App\Models\Whitelist;
use App\Models\Currency;
use App\Notifications\WhitelistApproved;
use App\Notifications\WhitelistPendingApproval;
use App\Notifications\WhitelistRejected;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Support\Facades\Response;

class WhitelistsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 25;
        $query  = Whitelist::query()
            ->with(['user', 'currency']);
        if (!empty($keyword)) {
            $query->where('user_id', 'LIKE', "%$keyword%")
                ->orWhere('currency_id', 'LIKE', "%$keyword%")
                ->orWhere('payout_address', 'LIKE', "%$keyword%")
                ->orWhere('approved', 'LIKE', "%$keyword%")
                ->orWhere('status', 'LIKE', "%$keyword%");
        }
        $pending =  $query->clone()->where('status', WhitelistStatus::REVIEW)->count();
        $whitelistsItems = $query->latest()->paginate($perPage);
        $whitelists = WhitelistResource::collection($whitelistsItems);
        return Inertia::render('Admin/Whitelists/Index', compact('whitelists', 'pending'));
    }

    /**
     * Download all whitelisted addresses on review.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download(Request $request)
    {
        $content = Whitelist::query()
            ->where('status', WhitelistStatus::REVIEW)
            ->pluck('payout_address')
            ->implode("\n");
        $filename = 'whitelisted_addresses_' . now()->format('Y-m-d') . '.txt';
        return Response::streamDownload(function () use ($content) {
            echo $content;
        }, $filename, [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }




    /**
     * Store a newly created whitelist address in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function massApprove(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate(['list' => ['required', 'string']]);
        $all = str($request->list)->replace([',', ' ', "\n"], ',')->explode(',')->map(fn($x) => trim($x))->all();
        $whitelist = Whitelist::whereIn('payout_address', $all)->with('user')->get();
        Whitelist::whereIn('payout_address', $all)->update([
            'approved' => true,
            'status' => WhitelistStatus::APPROVED
        ]);
        $whitelist->each(function (Whitelist $whitelist) {
            $whitelist->user->notify(new WhitelistApproved($whitelist));
        });
        return back();
    }


    /**
     * Mass reject whitelist
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function massReject(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate(['list' => ['required', 'string']]);
        $all = str($request->list)->replace([',', ' ', "\n"], ',')->explode(',')->map(fn($x) => trim($x))->all();
        $whitelist = Whitelist::whereIn('payout_address', $all)->with('user')->get();
        Whitelist::whereIn('payout_address', $all)->update([
            'approved' => false,
            'status' => WhitelistStatus::REJECTED
        ]);
        $whitelist->each(function (Whitelist $whitelist) {
            $whitelist->user->notify(new WhitelistRejected($whitelist));
        });
        return back();
    }

    /**
     * Approve a whitelist entry.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Whitelist  $whitelist
     * @param  string  $approvalToken
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(Request $request, Whitelist $whitelist, string $approvalToken)
    {
        // Verify the approval token
        if (!Hash::check($approvalToken, $whitelist->approval_token)) {
            return redirect()->route('whitelists.index')->with('error', 'Invalid approval token.');
        }

        // Check if the whitelist is already approved
        if ($whitelist->approved) {
            return redirect()->route('whitelists.index')->with('info', 'This address has already been approved.');
        }

        // Update the whitelist status
        $whitelist->approved = true;
        $whitelist->status = WhitelistStatus::APPROVED;
        $whitelist->save();

        // Notify the user
        $whitelist->user->notify(new WhitelistApproved($whitelist));

        return redirect()->route('whitelists.index')->with('success', 'Withdrawal address has been successfully approved.');
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show(Request $request, Whitelist $whitelist)
    {
        $whitelist->load(['user', 'currency']);
        return Inertia::render('Admin/Whitelists/Show', [
            'whitelist' => new WhitelistResource($whitelist)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, Whitelist $whitelist)
    {
        $whitelist->load(['user', 'currency']);
        return Inertia::render('Admin/Whitelists/Edit', [
            'whitelist' => new WhitelistResource($whitelist)
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
    public function update(Request $request, Whitelist $whitelist)
    {
        $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'currency_id' => ['required', 'integer', 'exists:currencies,id'],
            'payout_address' => ['required', 'string'],
            'approved' => ['required', 'boolean'],
        ]);

        $whitelist->user_id = $request->user_id;
        $whitelist->currency_id = $request->currency_id;
        $whitelist->payout_address = $request->payout_address;
        $whitelist->approved = $request->approved;
        $whitelist->status = $request->status;
        $whitelist->save();
        return back()->with('success', 'Whitelist updated!');
    }

    /**
     * toggle status of  the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function toggle(Request $request, Whitelist $whitelist)
    {
        $whitelist->active = !$whitelist->active;
        $whitelist->save();
        return back()->with('success', $whitelist->active ? __(' :name Whitelist Enabled !', ['name' => $whitelist->name]) : __(' :name  Whitelist Disabled!', ['name' => $whitelist->name]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Request $request, Whitelist $whitelist)
    {
        $whitelist->delete();
        return redirect()->route('whitelists.index')->with('success', 'Whitelist deleted!');
    }
}
