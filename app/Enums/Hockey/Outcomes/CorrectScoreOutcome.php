<?php

namespace App\Enums\Hockey\Outcomes;

use Str;

enum CorrectScoreOutcome: string
{
    case HOME_1_0 = 'home_1_0';
    case HOME_2_0 = 'home_2_0';
    case HOME_2_1 = 'home_2_1';
    case HOME_3_0 = 'home_3_0';
    case HOME_3_1 = 'home_3_1';
    case HOME_3_2 = 'home_3_2';
    case AWAY_1_0 = 'away_1_0';
    case AWAY_2_0 = 'away_2_0';
    case AWAY_2_1 = 'away_2_1';
    case AWAY_3_0 = 'away_3_0';
    case AWAY_3_1 = 'away_3_1';
    case AWAY_3_2 = 'away_3_2';
    case DRAW_0_0 = 'draw_0_0';
    case DRAW_1_1 = 'draw_1_1';
    case DRAW_2_2 = 'draw_2_2';
    case DRAW_3_3 = 'draw_3_3';
    case ANY_OTHER = 'any_other';

    public function homeGoals(): int
    {
        return (int) Str::before($this->value, '_');
    }

    public function awayGoals(): int
    {
        return (int) Str::after($this->value, '_');
    }

    public function name(): string
    {
        if ($this == self::ANY_OTHER) {
            return "Any Other Score";
        }
        return str_replace('_', '-',  formatName($this->value));
    }
}
