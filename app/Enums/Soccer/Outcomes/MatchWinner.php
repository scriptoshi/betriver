<?php

namespace App\Enums\Soccer\Outcomes;

enum MatchWinner: string
{
    case HOME = 'home';
    case DRAW = 'draw';
    case AWAY = 'away';
    public function name(): string
    {
        return formatName(match ($this) {
            self::HOME => 'Home',
            self::DRAW => 'Draw',
            self::AWAY => 'Away',
        });
    }
}
