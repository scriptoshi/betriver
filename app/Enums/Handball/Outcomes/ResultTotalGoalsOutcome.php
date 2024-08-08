<?php

namespace App\Enums\Handball\Outcomes;

enum ResultTotalGoalsOutcome: string
{
    case HOME_OVER_35 = 'home_over_3_5';
    case HOME_UNDER_35 = 'home_under_3_5';
    case DRAW_OVER_35 = 'draw_over_3_5';
    case DRAW_UNDER_35 = 'draw_under_3_5';
    case AWAY_OVER_35 = 'away_over_3_5';
    case AWAY_UNDER_35 = 'away_under_3_5';

    public function name(): string
    {
        $parts = explode('_', $this->value);
        $result = ucfirst($parts[0]);
        $overUnder = ucfirst($parts[1]);
        $goals = $parts[2] . '.' . $parts[3];
        return "{$result} & {$overUnder} {$goals}";
    }

    public function result(): string
    {
        return explode('_', $this->value)[0];
    }

    public function overUnder(): string
    {
        return explode('_', $this->value)[1];
    }

    public function threshold(): float
    {
        $parts = explode('_', $this->value);
        return (float)($parts[2] . '.' . $parts[3]);
    }
}
