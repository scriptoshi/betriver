<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NotInstalled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the application is already installed
        if ($this->isInstalled()) {
            return redirect('/');
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
        // You can customize this check based on your needs
        // For example, you might check for the existence of a specific file or database table
        return file_exists(storage_path('installed'));
    }
}
