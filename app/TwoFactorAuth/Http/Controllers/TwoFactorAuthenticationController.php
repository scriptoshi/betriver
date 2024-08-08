<?php

namespace App\TwoFactorAuth\Http\Controllers;

use App\TwoFactorAuth\Actions\DisableTwoFactorAuthentication;
use App\TwoFactorAuth\Actions\EnableTwoFactorAuthentication;
use App\TwoFactorAuth\Http\Responses\TwoFactorDisabledResponse;
use App\TwoFactorAuth\Http\Responses\TwoFactorEnabledResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TwoFactorAuthenticationController extends Controller
{
    /**
     * Enable two factor authentication for the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TwoFactorAuth\Actions\EnableTwoFactorAuthentication  $enable
     * @return \App\TwoFactorAuth\Http\Responses\TwoFactorEnabledResponse
     */
    public function store(Request $request, EnableTwoFactorAuthentication $enable)
    {
        $enable($request->user(), $request->boolean('force', false));
        return app(TwoFactorEnabledResponse::class);
    }

    /**
     * Disable two factor authentication for the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TwoFactorAuth\Actions\DisableTwoFactorAuthentication  $disable
     * @return \App\TwoFactorAuth\Http\Responses\TwoFactorDisabledResponse
     */
    public function destroy(Request $request, DisableTwoFactorAuthentication $disable)
    {
        $disable($request->user());
        return app(TwoFactorDisabledResponse::class);
    }
}
