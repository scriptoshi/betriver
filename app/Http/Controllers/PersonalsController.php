<?php

namespace App\Http\Controllers;

use App\Enums\PersonalBetEmails;
use App\Enums\PersonalLossLimitInterval;
use App\Enums\PersonalProofOfAddressType;
use App\Enums\PersonalProofOfIdentityType;
use App\Http\Controllers\Controller;
use App\Http\Resources\Personal as PersonalResource;
use App\Models\Personal;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class PersonalsController extends Controller
{

    /**
     * Update deposit limit for the user
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function limitDeposit(Request $request)
    {
        $request->validate([
            'daily_gross_deposit' => ['nullable', 'numeric'],
            'weekly_gross_deposit' => ['nullable', 'numeric'],
            'monthly_gross_deposit' => ['nullable', 'numeric'],
        ]);
        $personal = $request->user()->personal;
        $personal->daily_gross_deposit = $request->daily_gross_deposit ?? null;
        $personal->weekly_gross_deposit = $request->weekly_gross_deposit ?? null;
        $personal->monthly_gross_deposit = $request->monthly_gross_deposit ?? null;
        $personal->save();
        return back();
    }

    /**
     * Update the loss limit for the user
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function limitLoss(Request $request)
    {
        $request->validate([
            'loss_limit_interval' => ['nullable', 'string', new Enum(PersonalLossLimitInterval::class)],
            'loss_limit' => ['nullable', 'numeric'],
        ]);
        $personal = $request->user()->personal;

        $personal->loss_limit_interval = $request->loss_limit_interval ?? null;
        $personal->loss_limit = $request->loss_limit ?? null;

        $personal->save();
        return back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function limitStake(Request $request)
    {
        $request->validate([
            'stake_limit' => ['nullable', 'numeric'],
        ]);
        $personal = $request->user()->personal;
        if ($personal->stake_limit_at && now()->subWeek()->lt($personal->stake_limit_at))
            throw ValidationException::withMessages(['stake_limit' => [__('Cool off period is active')]]);
        $personal->stake_limit = $request->stake_limit;
        $personal->stake_limit_at = now();
        $personal->save();
        return back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function timeout(Request $request)
    {
        $request->validate([
            'days' => ['required', 'numeric'],
        ]);
        $personal = $request->user()->personal;
        $personal->time_out_at = now()->addDays($request->days);
        $personal->save();
        return back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function alerts(Request $request)
    {
        $request->validate([
            'bet_emails' => ['required', 'string', new Enum(PersonalBetEmails::class)],
            'mailing_list' => ['required', 'boolean'],
            'confirm_bets' => ['required', 'boolean']
        ]);
        $personal = $request->user()->personal;

        $personal->bet_emails = $request->bet_emails;
        $personal->mailing_list = $request->mailing_list;
        $personal->confirm_bets = $request->confirm_bets;
        $personal->save();
        return back();
    }
}
