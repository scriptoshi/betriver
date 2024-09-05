<?php

namespace App\Enums\Volleyball\Outcomes;

use App\Traits\Handicaps;
use Illuminate\Support\Str;

enum VolleyballAsianHandicapOutcome: string
{
    use Handicaps;
    case HOME_MINUS_2_5 = 'home_-2.5';
    case AWAY_PLUS_2_5 = 'away_+2.5';
    case HOME_MINUS_1_5 = 'home_-1.5';
    case AWAY_PLUS_1_5 = 'away_+1.5';
    case HOME_MINUS_0_5 = 'home_-0.5';
    case AWAY_PLUS_0_5 = 'away_+0.5';
    case HOME_PLUS_0_5 = 'home_+0.5';
    case AWAY_MINUS_0_5 = 'away_-0.5';
    case HOME_PLUS_1_5 = 'home_+1.5';
    case AWAY_MINUS_1_5 = 'away_-1.5';
    case HOME_PLUS_2_5 = 'home_+2.5';
    case AWAY_MINUS_2_5 = 'away_-2.5';

    public function team(): string
    {
        return Str::before($this->value, '_');
    }

    public function handicapValue(): float
    {
        return (float) Str::after($this->value, '_');
    }

    public function name(): string
    {
        $team = ucfirst($this->team());
        $handicap = $this->handicapValue();
        $handicapStr = $handicap > 0 ? "+{$handicap}" : $handicap;
        return formatName("{$team} ({$handicapStr})");
    }
}
