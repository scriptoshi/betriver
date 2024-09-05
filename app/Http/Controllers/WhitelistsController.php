<?php

namespace App\Http\Controllers;

use App\Enums\WhitelistStatus;
use App\Enums\WithdrawGateway;
use App\Http\Controllers\Controller;
use App\Http\Resources\Whitelist as WhitelistResource;
use App\Models\Whitelist;
use App\Models\Currency;
use App\Models\User;
use App\Notifications\WhitelistApprovalRequest;
use App\Notifications\WhitelistApproved;
use App\Notifications\WhitelistPendingApproval;
use App\Notifications\WhitelistRemovalRequest;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Inertia\Inertia;

class WhitelistsController extends Controller
{

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 10;
        $query  = Whitelist::query()
            ->with(['user', 'currency'])
            ->where('user_id', $request->user()->id);
        if (!empty($keyword)) {
            $query->where('uuid', 'LIKE', "%$keyword%")
                ->orWhere('currency_id', 'LIKE', "%$keyword%")
                ->orWhere('payout_address', 'LIKE', "%$keyword%")
                ->orWhere('status', 'LIKE', "%$keyword%");
        }
        $whitelistsItems = $query->latest()->paginate($perPage);
        $whitelists = WhitelistResource::collection($whitelistsItems);
        return Inertia::render('Whitelists/Index', [
            'gateways' => WithdrawGateway::getNames(),
            'whitelists' => $whitelists,
            'currencies' => Currency::active()->get()->groupBy('gateway'),
        ]);
    }


    /**
     * Store a newly created whitelist address in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'currency_id' => ['required', 'integer', 'exists:currencies,id'],
            'payout_address' => ['required', 'string'],
        ]);

        $user = $request->user();
        $currency = Currency::findOrFail($request->currency_id);

        // Check if user already has a whitelist for this currency
        $existingWhitelist = $user->whitelists()->where('currency_id', $currency->id)->first();

        if ($existingWhitelist) {
            return redirect()->back()->with('error', 'You already have a whitelisted address for this currency. contact support to remove it');
        }

        $whitelist = new Whitelist([
            'currency_id' => $currency->id,
            'payout_address' => $request->payout_address,
            'approved' => false,
            'status' => WhitelistStatus::PENDING,
        ]);
        // Generate approval token
        $approvalToken = Str::random(64);
        $whitelist->approval_token = Hash::make($approvalToken);
        $user->whitelists()->save($whitelist);
        // Send notification to user
        $user->notify(new WhitelistPendingApproval($whitelist, $approvalToken));
        return redirect()->route('whitelists.index')->with('success', 'Whitelist address submitted for approval. Please check your email for the approval link.');
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

        if (WithdrawGateway::from($whitelist->currency->gateway)->settings('auto_approve_withdraw_address')) {
            // Update the whitelist status
            $whitelist->approved = true;
            $whitelist->status = WhitelistStatus::APPROVED;
            $whitelist->save();
            // Notify the user
            $whitelist->user->notify(new WhitelistApproved($whitelist));
            return redirect()->route('whitelists.index')->with('success', 'Withdrawal address has been successfully approved.');
        }
        $whitelist->status = WhitelistStatus::REVIEW;
        $whitelist->save();
        return redirect()->route('whitelists.index')->with('success', 'Withdrawal address has been successfully submitted for review.');
    }



    /**
     * Initiate the removal process for a whitelisted address.
     *
     * @param Request $request
     * @param Whitelist $whitelist
     * @return \Illuminate\Http\RedirectResponse
     */
    public function initiateRemoval(Request $request, Whitelist $whitelist)
    {
        // Check if the whitelist belongs to the authenticated user
        Gate::authorize('update', $whitelist);
        // Generate a removal token
        $removalToken = Str::random(64);
        $whitelist->update(['removal_token' => $removalToken]);
        // Send 2FA verification email
        $request->user()->notify(new WhitelistRemovalRequest($whitelist, $removalToken));

        return redirect()->back()->with('success', 'A verification email has been sent to approve the removal process.');
    }

    /**
     * Complete the removal process for a whitelisted address after 2FA verification.
     *
     * @param Request $request
     * @param Whitelist $whitelist
     * @param string $token
     * @return \Illuminate\Http\RedirectResponse
     */
    public function completeRemoval(Request $request, Whitelist $whitelist, string $token)
    {
        Gate::authorize('update', $whitelist);

        // Verify the removal token
        if ($whitelist->removal_token !== $token) {
            return redirect()->route('whitelists.index')->with('error', 'Invalid or expired removal token.');
        }

        // Remove the whitelist
        $whitelist->forceDelete();
        return redirect()->route('whitelists.index')->with('success', 'Whitelisted address has been successfully removed.');
    }
}
