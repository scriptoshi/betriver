<?php

namespace App\Actions;

use Illuminate\Support\Facades\Cache;
use App\Models\Setting;
use Illuminate\Support\Collection;

use function Clue\StreamFilter\fun;

class Settings
{
    /**
     * Cache key.
     *
     * @var string
     */
    protected static $cacheKey = 'betriver-settings-cache';
    protected static $groupKey = 'betriver-settings-cache';

    /**
     * Get all settings from storage as key value pair.
     *
     * @param  bool  $fresh  ignore cached
     * @return Collection
     */
    public static function all($fresh = false)
    {
        if ($fresh) {
            $dot = Setting::all()->flatMap(fn (Setting $s) => ["{$s->group}.{$s->name}" => $s->val]);
            return Setting::query()->pluck('val', 'name')->merge($dot);
        }

        return Cache::rememberForever(static::$groupKey, function () {
            $dot = Setting::all()->flatMap(fn (Setting $s) => ["{$s->group}.{$s->name}" => $s->val]);
            return  Setting::query()->pluck('val', 'name')->merge($dot);
        });
    }

    /**
     * Get all settings from storage as key value pair.
     *
     * @param  bool  $fresh  ignore cached
     * @return Collection
     */
    public static function group($fresh = false)
    {
        if ($fresh) Cache::forget(static::$groupKey);
        return Cache::rememberForever(static::$cacheKey, function () {
            $dot = Setting::all()->groupBy('group')->flatMap(function (Setting $s) {
                $val = in_array($s->val, ['true', 'false']) ? json_decode($s->val) : $s->val;
                return ["{$s->group}.{$s->name}" => $val];
            });
            return  Setting::query()->pluck('val', 'name')->merge($dot);
        });
    }

    /**
     * Get all group settings from storage as key value pair.
     *
     * @param  bool  $fresh  ignore cached
     * @return Collection
     */
    public static function for($group)
    {
        if (is_array($group)) {
            $settings = Setting::query()->whereIn('group', $group)->get();
            return $settings->groupBy('group')->mapWithKeys(function (Collection $list, $grp) {
                return [$grp => $list->flatMap(function (Setting $s) {
                    $val = in_array($s->val, ['true', 'false']) ? json_decode($s->val) : $s->val;
                    return [$s->name => $val];
                })];
            });
        }
        return Setting::query()
            ->where('group', $group)
            ->get()
            ->flatMap(function (Setting $s) {
                $val = in_array($s->val, ['true', 'false']) ? json_decode($s->val) : $s->val;
                return [$s->name => $val];
            });
    }

    /**
     * Get all group settings from storage as key value pair.
     *
     * @param  bool  $fresh  ignore cached
     * @return Collection
     */
    public static function json($group)
    {
        return Setting::query()->where('group', $group)->pluck('val', 'name');
    }

    /**
     * Get a setting from storage by key.
     *
     * @param  string  $key
     * @param  null  $default
     * @param  bool  $fresh
     */
    public static function get($key, $default = null, $fresh = false): mixed
    {
        $setting = static::all($fresh)->get($key, $default);
        if (in_array($setting, ['true', 'false']))
            return json_decode($setting);
        return $setting;
    }

    /**
     * Save a setting in storage.
     *
     * @param $key string|array
     * @param $val string|mixed
     */
    public static function set($key, $val = null): mixed
    {
        // if its an array, upsert
        if (is_array($key)) {
            Setting::query()
                ->upsert(
                    collect($key)->map(fn ($value, $name) => ['name' => $name, 'val' => $value])->all(),
                    uniqueBy: ['name'],
                    update: ['val']
                );
            Cache::forget(static::$cacheKey);
            return true;
        }
        if (str($key)->contains('.')) {
            [$group, $name] = explode('.', $key);
            Setting::query()
                ->updateOrCreate(
                    ['name' => trim($name), 'group' => trim($group)],
                    ['val' => trim($val)]
                );
            Cache::forget(static::$cacheKey);
            return $val;
        }
        Setting::query()
            ->updateOrCreate(
                ['name' => $key],
                ['val' => $val]
            );
        Cache::forget(static::$cacheKey);
        return $val;
    }

    /**
     * Check if setting with key exists.
     *
     * @param $key
     */
    public function has($key): bool
    {
        return static::all()->has($key);
    }

    /**
     * Remove a setting from storage.
     *
     * @param $key
     */
    public function remove($key): mixed
    {
        $deleted = Setting::query()
            ->where('name', $key)
            ->delete();
        Cache::forget(static::$cacheKey);
        return $deleted;
    }

    public function refresh()
    {
        Cache::forget(static::$cacheKey);
        Cache::forget(static::$groupKey);
    }
}
