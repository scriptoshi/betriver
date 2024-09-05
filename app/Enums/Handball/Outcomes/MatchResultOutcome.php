<?php

namespace App\Enums\Handball\Outcomes;

enum MatchResultOutcome: string
{
    case HOME = 'home';
    case DRAW = 'draw';
    case AWAY = 'away';

    public function name(): string
    {
        return match ($this) {
            self::HOME => '{home}',
            self::DRAW => 'Draw',
            self::AWAY => '{away}',
        };
    }
}
