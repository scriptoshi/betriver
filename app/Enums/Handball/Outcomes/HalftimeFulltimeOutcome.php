<?php

namespace App\Enums\Handball\Outcomes;

enum HalftimeFulltimeOutcome: string
{
    case HOME_HOME = 'home_home';
    case HOME_DRAW = 'home_draw';
    case HOME_AWAY = 'home_away';
    case DRAW_HOME = 'draw_home';
    case DRAW_DRAW = 'draw_draw';
    case DRAW_AWAY = 'draw_away';
    case AWAY_HOME = 'away_home';
    case AWAY_DRAW = 'away_draw';
    case AWAY_AWAY = 'away_away';

    public function name(): string
    {
        $parts = explode('_', $this->value);
        $halftime = ucfirst($parts[0]);
        $fulltime = ucfirst($parts[1]);
        return "{$halftime}/{$fulltime}";
    }

    public function halftimeResult(): string
    {
        return explode('_', $this->value)[0];
    }

    public function fulltimeResult(): string
    {
        return explode('_', $this->value)[1];
    }
}
