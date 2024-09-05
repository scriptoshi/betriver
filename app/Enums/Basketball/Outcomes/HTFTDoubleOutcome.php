<?php

namespace App\Enums\Basketball\Outcomes;

use Str;

enum HTFTDoubleOutcome: string
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

    public function halfTimeResult(): string
    {
        return Str::before($this->value, '_');
    }

    public function fullTimeResult(): string
    {
        return Str::after($this->value, '_');
    }

    public function name(): string
    {
        $ht = formatName(ucfirst($this->halfTimeResult()));
        $ft = formatName(ucfirst($this->fullTimeResult()));
        return "{$ht}/{$ft}";
    }
}
