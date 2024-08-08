<?php

namespace App\Enums\Soccer\Outcomes;

use Illuminate\Support\Str;

enum WinnerAndTotalGoalsOutcome: string
{
    case HOME_UNDER_15 = 'home_under_1.5';
    case HOME_OVER_15 = 'home_over_1.5';
    case HOME_UNDER_25 = 'home_under_2.5';
    case HOME_OVER_25 = 'home_over_2.5';
    case HOME_UNDER_35 = 'home_under_3.5';
    case HOME_OVER_35 = 'home_over_3.5';
    case HOME_UNDER_45 = 'home_under_4.5';
    case HOME_OVER_45 = 'home_over_4.5';

    case DRAW_UNDER_15 = 'draw_under_1.5';
    case DRAW_OVER_15 = 'draw_over_1.5';
    case DRAW_UNDER_25 = 'draw_under_2.5';
    case DRAW_OVER_25 = 'draw_over_2.5';
    case DRAW_UNDER_35 = 'draw_under_3.5';
    case DRAW_OVER_35 = 'draw_over_3.5';
    case DRAW_UNDER_45 = 'draw_under_4.5';
    case DRAW_OVER_45 = 'draw_over_4.5';

    case AWAY_UNDER_15 = 'away_under_1.5';
    case AWAY_OVER_15 = 'away_over_1.5';
    case AWAY_UNDER_25 = 'away_under_2.5';
    case AWAY_OVER_25 = 'away_over_2.5';
    case AWAY_UNDER_35 = 'away_under_3.5';
    case AWAY_OVER_35 = 'away_over_3.5';
    case AWAY_UNDER_45 = 'away_under_4.5';
    case AWAY_OVER_45 = 'away_over_4.5';

    public function result(): string
    {
        return Str::before($this->value, '_');
    }

    public function goalRange(): string
    {
        return Str::after($this->value, '_');
    }

    public function name(): string
    {
        $result = Str::ucfirst($this->result());
        $goalRange = str_replace('_', ' ', Str::ucfirst($this->goalRange()));
        return formatName("{$result} & {$goalRange}");
    }
}
