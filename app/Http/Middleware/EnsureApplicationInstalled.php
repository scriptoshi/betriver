<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApplicationInstalled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->isInstalled()) {
            return redirect()->route('installer.welcome');
        }

        return $next($request);
    }

    /**
     * Check if the application is installed.
     *
     * @return bool
     */
    private function isInstalled(): bool
    {
        return file_exists(storage_path('installed'));
    }
}
