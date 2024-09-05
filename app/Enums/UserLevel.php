<?php

namespace App\Enums;

enum UserLevel: string
{
    case ONE = 'level_one';
    case TWO = 'level_two';
    case THREE = 'level_three';

    public function config()
    {
        $config = match ($this) {
            static::ONE =>  settings()->for('level_one'),
            static::TWO =>  settings()->for('level_two'),
            static::THREE =>  settings()->for('level_three')
        };
        $withCamels =  collect($config)->flatMap(function ($conf, $key) {
            return [$key => $conf, str($key)->camel()->value() => $conf];
        })->all();
        $withCamels['description'] = __($withCamels['description'], $withCamels);
        $withCamels['limits'] = __($withCamels['limits'], $withCamels);
        return $withCamels;
    }

    public function settings($setting): string
    {
        return match ($this) {
            static::ONE =>  settings("level_one.$setting"),
            static::TWO =>  settings("level_two.$setting"),
            static::THREE =>  settings("level_three.$setting")
        };
    }

    public function level(): int
    {
        return match ($this) {
            static::ONE =>  1,
            static::TWO =>  2,
            static::THREE =>  3
        };
    }

    public function name(): string
    {
        return match ($this) {
            static::ONE =>  'Level One',
            static::TWO =>  'Level Two',
            static::THREE => 'Level Three'
        };
    }

    public static function make(int $lvl): static
    {
        return match ($lvl) {
            1 => static::ONE,
            2 => static::TWO,
            3 => static::THREE
        };
    }
}
