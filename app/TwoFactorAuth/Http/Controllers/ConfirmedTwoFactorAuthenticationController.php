<?php

namespace App\TwoFactorAuth\Http\Controllers;

use App\TwoFactorAuth\Actions\ConfirmTwoFactorAuthentication;
use App\TwoFactorAuth\Http\Responses\TwoFactorConfirmedResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ConfirmedTwoFactorAuthenticationController extends Controller
{
    /**
     * Enable two factor authentication for the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TwoFactorAuth\Actions\ConfirmTwoFactorAuthentication  $confirm
     * @return \App\TwoFactorAuth\Http\Responses\TwoFactorConfirmedResponse
     */
    public function store(Request $request, ConfirmTwoFactorAuthentication $confirm)
    {
        $confirm($request->user(), $request->input('code'));
        return app(TwoFactorConfirmedResponse::class);
    }
}
