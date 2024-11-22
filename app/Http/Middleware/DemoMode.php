<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DemoMode
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (config('app.demo') === true) {
            return redirect()
                ->back()
                ->with('error', 'Updating config is disabled on the demo');
        }

        return $next($request);
    }
}
