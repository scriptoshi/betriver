<?php

namespace App\Enums\Soccer\Outcomes;

enum WinningMarginOutcome: string
{
    case DRAW = 'draw';
    case HOME_1 = 'home_1';
    case HOME_2 = 'home_2';
    case HOME_3PLUS = 'home_3plus';
    case AWAY_1 = 'away_1';
    case AWAY_2 = 'away_2';
    case AWAY_3PLUS = 'away_3plus';

    public function name(): string
    {
        return formatName(match ($this) {
            self::DRAW => 'Draw',
            self::HOME_1 => '{home} by 1',
            self::HOME_2 => '{home} by 2',
            self::HOME_3PLUS => '{home} by 3+',
            self::AWAY_1 => '{away} by 1',
            self::AWAY_2 => '{away} by 2',
            self::AWAY_3PLUS => '{away} by 3+',
        });
    }
}
