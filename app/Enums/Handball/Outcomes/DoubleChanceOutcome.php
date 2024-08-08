<?php

namespace App\Enums\Handball\Outcomes;

enum DoubleChanceOutcome: string
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

    public function includes(string $result): bool
    {
        return match ($this) {
            self::HOME_DRAW => $result === 'home' || $result === 'draw',
            self::HOME_AWAY => $result === 'home' || $result === 'away',
            self::DRAW_AWAY => $result === 'draw' || $result === 'away',
        };
    }
}
