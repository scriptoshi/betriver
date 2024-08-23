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
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Grammars\Grammar;
use Illuminate\Database\Schema\Grammars\SQLiteGrammar;
use Illuminate\Support\Fluent;
use Illuminate\Support\Str;

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
                ->filter(fn(ConnectionProvider $provider) => $provider->active())
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

        /**
         * Setup check macro
         */
        $this->dbMacros();
    }

    public function dbMacros()
    {
        Blueprint::macro('check', function (string $expression, ?string $constraint = null) {
            /** @var Blueprint $this */
            $constraint = $constraint ?: $this->createCheckName($expression);

            return $this->addCommand('check', compact('expression', 'constraint'));
        });

        Blueprint::macro('dropCheck', function (string|array $constraints) {
            /** @var Blueprint $this */
            $constraints = is_array($constraints) ? $constraints : func_get_args();

            return $this->addCommand('dropCheck', compact('constraints'));
        });

        Blueprint::macro('createCheckName', function (string $expression) {
            /** @var Blueprint $this */
            return (string) Str::of("{$this->prefix}{$this->table}_{$expression}_check")
                ->replaceMatches('#[\W_]+#', '_')
                ->trim('_')
                ->lower();
        });

        Grammar::macro('compileCheck', function (Blueprint $blueprint, Fluent $command) {
            /** @var Grammar $this */
            return sprintf(
                'alter table %s add constraint %s check (%s)',
                $this->wrapTable($blueprint),
                $this->wrap($command->constraint),
                $command->expression,
            );
        });

        Grammar::macro('compileDropCheck', function (Blueprint $blueprint, Fluent $command) {
            /** @var Grammar $this */
            $constraints = $this->prefixArray('drop constraint', $this->wrapArray($command->constraints));
            return 'alter table ' . $this->wrapTable($blueprint) . ' ' . implode(', ', $constraints);
        });
        Grammar::macro('handleInvalidCheckConstraintDriver', function () {
            /** @var Grammar $this */
            if (config('check-constraints.sqlite.throw', true)) {
                throw new \RuntimeException('SQLite driver does not support check constraints.');
            }
            return null;
        });
    }
}
