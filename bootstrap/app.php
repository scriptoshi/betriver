<?php

use App\Http\Middleware\EnsureApplicationInstalled;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\NotInstalled;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware(['web', IsAdmin::class, 'auth', 'verified'])
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));
            Route::middleware(['web', NotInstalled::class])
                ->prefix('install')
                ->name('installer.')
                ->withoutMiddleware(EnsureApplicationInstalled::class)
                ->group(base_path('routes/installer.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \App\Http\Middleware\Timeout::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
            EnsureApplicationInstalled::class,
        ])->validateCsrfTokens(except: ['webhooks/*']);

        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
