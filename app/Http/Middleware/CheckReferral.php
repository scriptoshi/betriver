<?php

namespace App\Http\Middleware;

use App\Models\Account;
use Closure;
use App\Web3\AddressValidator;

class CheckReferral
{

    public function handle($request, Closure $next)
    {
        if (auth()->check() || $request->hasCookie('referral'))
            return $next($request);
        if (!$request->query('ref'))
            return $next($request);
        $ref = $request->query('ref');
        return redirect($request->fullUrl())->withCookie(cookie()->forever('referral', $ref));
    }
}
