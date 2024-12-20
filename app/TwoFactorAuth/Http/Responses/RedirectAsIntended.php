<?php

namespace App\TwoFactorAuth\Http\Responses;

use Illuminate\Contracts\Support\Responsable;

class RedirectAsIntended implements Responsable
{
    /**
     * Create a new class instance.
     *
     * @param  string  $name
     * @return void
     */
    public function __construct(public string $name)
    {
        //
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        return redirect()->intended(route('dashboard', absolute: false));
    }
}
