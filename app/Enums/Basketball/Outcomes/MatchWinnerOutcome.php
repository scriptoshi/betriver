<?php

namespace App\Enums\Basketball\Outcomes;

enum MatchWinnerOutcome: string
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
