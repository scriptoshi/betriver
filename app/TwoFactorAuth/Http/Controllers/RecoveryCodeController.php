<?php

namespace App\TwoFactorAuth\Http\Controllers;

use App\TwoFactorAuth\Actions\GenerateNewRecoveryCodes;
use App\TwoFactorAuth\Http\Responses\RecoveryCodesGeneratedResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class RecoveryCodeController extends Controller
{
    /**
     * Get the two factor authentication recovery codes for authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (
            !$request->user()->two_factor_secret ||
            !$request->user()->two_factor_recovery_codes
        ) {
            return [];
        }

        return response()->json(json_decode(decrypt(
            $request->user()->two_factor_recovery_codes
        ), true));
    }

    /**
     * Generate a fresh set of two factor authentication recovery codes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param   \App\TwoFactorAuth\Actions\GenerateNewRecoveryCodes  $generate
     * @return \App\TwoFactorAuth\Http\Responses\RecoveryCodesGeneratedResponse
     */
    public function store(Request $request, GenerateNewRecoveryCodes $generate)
    {
        $generate($request->user());
        return app(RecoveryCodesGeneratedResponse::class);
    }
}
