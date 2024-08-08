<?php

namespace App\Enums\Afl\Outcomes;

use Illuminate\Support\Str;

enum AFLHalfTimeFullTimeOutcome: string
{
    case HOME_HOME = 'H/H';
    case HOME_DRAW = 'H/D';
    case HOME_AWAY = 'H/A';
    case DRAW_HOME = 'D/H';
    case DRAW_DRAW = 'D/D';
    case DRAW_AWAY = 'D/A';
    case AWAY_HOME = 'A/H';
    case AWAY_DRAW = 'A/D';
    case AWAY_AWAY = 'A/A';

    public function name(): string
    {
        return match ($this) {
            self::HOME_HOME => '{home}/{home}',
            self::HOME_DRAW => '{home}/Draw',
            self::HOME_AWAY => '{home}/{away}',
            self::DRAW_HOME => 'Draw/{home}',
            self::DRAW_DRAW => 'Draw/Draw',
            self::DRAW_AWAY => 'Draw/{away}',
            self::AWAY_HOME => '{away}/{home}',
            self::AWAY_DRAW => '{away}/Draw',
            self::AWAY_AWAY => '{away}/{away}',
        };
    }

    public function halfTimeResult(): string
    {
        return Str::before($this->value, '/');
    }

    public function fullTimeResult(): string
    {
        return Str::after($this->value, '/');
    }
}
