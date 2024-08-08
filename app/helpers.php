<?php

use App\Actions\Settings;

if (!function_exists('settings')) {

    /**
     * Get app setting stored in db.
     *
     * @param $key
     * @param  null  $default
     * @return mixed
     */
    function settings($key = null, $default = null)
    {
        $setting = app(Settings::class);
        if (is_null($key)) {
            return $setting;
        }
        if (is_array($key)) {
            return $setting->set($key);
        }
        return $setting->get($key, value($default));
    }
}

if (!function_exists('formatName')) {

    /**
     * Get app setting stored in db.
     *
     * @param $key
     * @param  null  $default
     * @return mixed
     */
    function formatName($name)
    {
        return Str::of($name)->replace(
            ['Home Team', 'Away Team', 'home', 'away',  'Home', 'Away', 'Both Teams', 'Either Teams'],
            ['{home}', '{away}', '{home}', '{away}', '{home}', '{away}', '{home} and {away}', 'Either {home} OR {away}']
        );
    }
}
