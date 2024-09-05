<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Timeout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && !$request->routeIs('timeout') && !$request->routeIs('logout')) {
            $user = Auth::user();
            if ($user->personal && $user->personal->time_out_at) {
                $timeOutAt = $user->personal->time_out_at;

                if ($timeOutAt instanceof Carbon && $timeOutAt->isFuture()) {
                    return redirect()->route('timeout');
                }
            }
        }

        return $next($request);
    }
}
