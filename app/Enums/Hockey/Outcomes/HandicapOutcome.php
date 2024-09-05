<?php

namespace App\Enums\Hockey\Outcomes;

use App\Traits\Handicaps;
use Illuminate\Support\Str;

enum HandicapOutcome: string
{
    use Handicaps;
    case HOME_MINUS_25 = 'home_-2.5';
    case AWAY_PLUS_25 = 'away_+2.5';
    case HOME_MINUS_2 = 'home_-2';
    case AWAY_PLUS_2 = 'away_+2';
    case HOME_MINUS_15 = 'home_-1.5';
    case AWAY_PLUS_15 = 'away_+1.5';
    case HOME_MINUS_1 = 'home_-1';
    case AWAY_PLUS_1 = 'away_+1';
    case HOME_MINUS_05 = 'home_-0.5';
    case AWAY_PLUS_05 = 'away_+0.5';
    case HOME_PLUS_05 = 'home_+0.5';
    case AWAY_MINUS_05 = 'away_-0.5';
    case HOME_PLUS_1 = 'home_+1';
    case AWAY_MINUS_1 = 'away_-1';
    case HOME_PLUS_15 = 'home_+1.5';
    case AWAY_MINUS_15 = 'away_-1.5';
    case HOME_PLUS_2 = 'home_+2';
    case AWAY_MINUS_2 = 'away_-2';
    case HOME_PLUS_25 = 'home_+2.5';
    case AWAY_MINUS_25 = 'away_-2.5';

    public function team(): string
    {
        return Str::before($this->value, '_');
    }

    public function handicap(): float
    {
        return (float) Str::after($this->value, '_');
    }

    public function name(): string
    {
        $team = Str::ucfirst($this->team());
        $handicap = $this->handicap();
        $handicapStr = $handicap > 0 ? "+{$handicap}" : $handicap;
        return formatName("{$team} ({$handicapStr})");
    }
}
