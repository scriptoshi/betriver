<?php

namespace App\TwoFactorAuth\Http\Controllers;

use App\TwoFactorAuth\Http\Requests\TwoFactorLoginRequest;
use App\TwoFactorAuth\Http\Responses\FailedTwoFactorLoginResponse;
use App\TwoFactorAuth\Http\Responses\TwoFactorLoginResponse;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;

class TwoFactorAuthenticatedSessionController extends Controller
{
    /**
     * The guard implementation.
     *
     * @var \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected $guard;

    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Contracts\Auth\StatefulGuard  $guard
     * @return void
     */
    public function __construct(StatefulGuard $guard)
    {
        $this->guard = $guard;
    }

    /**
     * Show the two factor authentication challenge view.
     *
     * @param  \App\TwoFactorAuth\Http\Requests\TwoFactorLoginRequest $request
     * @return \Inertia\Response
     */
    public function create(TwoFactorLoginRequest $request): Response
    {
        if (!$request->hasChallengedUser()) {
            throw new HttpResponseException(redirect()->route('login'));
        }
        return Inertia::render('Auth/TwoFactorChallenge');
    }

    /**
     * Attempt to authenticate a new session using the two factor authentication code.
     *
     * @param   \App\TwoFactorAuth\Http\Requests\TwoFactorLoginRequest  $request
     * @return mixed
     */
    public function store(TwoFactorLoginRequest $request)
    {
        $user = $request->challengedUser();

        if ($code = $request->validRecoveryCode()) {
            $user->replaceRecoveryCode($code);
        } elseif (!$request->hasValidCode()) {
            return app(FailedTwoFactorLoginResponse::class)->toResponse($request);
        }

        $this->guard->login($user, $request->remember());

        $request->session()->regenerate();

        return app(TwoFactorLoginResponse::class);
    }
}
