<?php

namespace App\Enums\Volleyball\Outcomes;

use Illuminate\Support\Str;

enum VolleyballMatchResultOutcome: string
{
    case HOME = 'home';
    case AWAY = 'away';

    public function name(): string
    {
        return match ($this) {
            self::HOME => '{home}',
            self::AWAY => '{away}',
        };
    }
}
