<?php

namespace App\TwoFactorAuth\Http\Responses;

use App\TwoFactorAuth\Actions\ThrottleKey;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\RateLimiter;

class LockoutResponse
{


    /**
     * Create a new response instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        return with(RateLimiter::availableIn(ThrottleKey::from($request)), function ($seconds) {
            throw ValidationException::withMessages([
                'email' => [
                    trans('auth.throttle', [
                        'seconds' => $seconds,
                        'minutes' => ceil($seconds / 60),
                    ]),
                ],
            ])->status(Response::HTTP_TOO_MANY_REQUESTS);
        });
    }
}
