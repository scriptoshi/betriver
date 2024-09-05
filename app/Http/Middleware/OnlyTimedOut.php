<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OnlyTimedOut
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
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (!$user->personal || !$user->personal->time_out_at) {
            return redirect()->route('home');
        }

        $timeOutAt = $user->personal->time_out_at;

        if (!($timeOutAt instanceof Carbon) || !$timeOutAt->isFuture()) {
            return redirect()->route('home');
        }

        return $next($request);
    }
}
