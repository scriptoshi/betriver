<?php

namespace App\Enums;

use Closure;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Laravel\Socialite\Two\FacebookProvider;
use Laravel\Socialite\Two\GithubProvider;

enum ConnectionProvider: string
{
    case GOOGLE = 'google'; //uses onetap;
    case GITHUB = 'github';
    case FACEBOOK = 'facebook';
    /**
     * TODO
     * No user email??
     * PENDING: ask user to provide email
     */
    //case TELEGRAM = 'telegram';
    //case TWITTER = 'twitter';
    //case TWITTER_OAUTH_2 = 'twitter-oauth-2';

    /**
     * closure to initialize the socialite provider
     */
    public function driver(): Closure
    {
        return fn() => Socialite::buildProvider(
            $this->provider(),
            [...$this->config(), 'redirect' => $this->callback()]
        );
    }

    /**
     * get the socialite driver
     */
    public function callback()
    {
        return route('connections.callback', ['provider' => $this->value]);
    }

    /**
     * get the socialite driver
     * env takes precedence
     */
    public function config($name = null)
    {
        $config = array_filter(config('services.' . $this->value, []));
        $settings = settings()->for($this->value);
        if ($name) {
            return  $config[$name] ?? $settings[$name] ??  null;
        }
        return collect(array_merge($settings->all(), $config));
    }

    /**
     * get the socialite provider driver
     */
    public function provider()
    {
        return match ($this) {
            static::GOOGLE => GoogleProvider::class,
            static::FACEBOOK => FacebookProvider::class,
            static::GITHUB => GithubProvider::class,
        };
    }

    /**
     * TODO
     * Custom scope 
     */
    public function active(): bool
    {
        return str(settings("{$this->value}.enable"))->toBoolean()
            && !!settings("{$this->value}.client_id");
    }

    /**
     * TODO
     * Custom scope 
     */
    public function scopes() {}
}
