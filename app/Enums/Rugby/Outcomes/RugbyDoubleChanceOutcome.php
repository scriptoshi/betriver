<?php

namespace App\Enums\Rugby\Outcomes;

enum RugbyDoubleChanceOutcome: string
{
    case HOME_DRAW = 'home_draw';
    case HOME_AWAY = 'home_away';
    case DRAW_AWAY = 'draw_away';

    public function name(): string
    {
        return match ($this) {
            self::HOME_DRAW => '{home} or Draw',
            self::HOME_AWAY => '{home} or {away}',
            self::DRAW_AWAY => 'Draw or {away}',
        };
    }
}
