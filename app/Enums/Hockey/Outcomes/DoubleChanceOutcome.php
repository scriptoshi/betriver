<?php

namespace App\Enums\Hockey\Outcomes;

enum DoubleChanceOutcome: string
{
    case HOME_OR_DRAW = 'home_or_draw';
    case AWAY_OR_DRAW = 'away_or_draw';
    case HOME_OR_AWAY = 'home_or_away';

    public function name(): string
    {
        return match ($this) {
            self::HOME_OR_DRAW => '{home} or Draw',
            self::AWAY_OR_DRAW => '{away} or Draw',
            self::HOME_OR_AWAY => '{home} or {away}',
        };
    }
}
