<?php

namespace App\Enums\Soccer\Outcomes;

enum MatchWinner: string
{
    case HOME = 'home';
    case AWAY = 'away';
    case DRAW = 'draw';

    public function name(): string
    {
        return formatName(match ($this) {
            self::HOME => 'Home',
            self::AWAY => 'Away',
            self::DRAW => 'Draw',
        });
    }
}
