<?php

namespace App\Enums\Rugby\Outcomes;

enum RugbyMatchResultOutcome: string
{
    case HOME = 'home';
    case AWAY = 'away';
    case DRAW = 'draw';

    public function name(): string
    {
        return match ($this) {
            self::HOME => '{home}',
            self::AWAY => '{away}',
            self::DRAW => 'Draw',
        };
    }
}
