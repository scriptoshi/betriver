<?php

namespace App\Providers;

use App\Enums\ConnectionProvider;
use App\Enums\MailDrivers;
use Exception;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /**
         * socialite drivers
         */
        try {
            DB::connection()->getPdo();
            // setup socialite driver
            collect(ConnectionProvider::cases())
                ->filter(fn (ConnectionProvider $provider) => $provider->active())
                ->each(function (ConnectionProvider $provider) {
                    Socialite::extend($provider->value, $provider->driver());
                });
            // boot mail driver 
            if ($mailer = settings('notifications.mailer')) {
                Mail::extend($mailer, MailDrivers::from($mailer)->driver());
                Mail::alwaysFrom(settings('notifications.from_address'), settings('notifications.from_name'));
            }
        } catch (Exception $e) {
        }

        JsonResource::withoutWrapping();
        //throttle two-factor auth route
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
