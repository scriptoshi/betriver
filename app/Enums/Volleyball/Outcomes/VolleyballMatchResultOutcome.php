<?php

namespace App\Enums\Volleyball\Outcomes;

use Illuminate\Support\Str;

enum VolleyballMatchResultOutcome: string
{
    case HOME = 'home';
    case DRAW = 'draw';
    case AWAY = 'away';


    public function name(): string
    {
        return match ($this) {
            self::HOME => '{home}',
            self::DRAW => 'draw',
            self::AWAY => '{away}',
        };
    }
}
