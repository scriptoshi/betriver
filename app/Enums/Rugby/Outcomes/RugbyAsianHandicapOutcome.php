<?php

namespace App\Enums\Rugby\Outcomes;

use Illuminate\Support\Str;

enum RugbyAsianHandicapOutcome: string
{
    case HOME_MINUS_22_5 = 'home_-22.5';
    case AWAY_PLUS_22_5 = 'away_+22.5';
    case HOME_MINUS_17_5 = 'home_-17.5';
    case AWAY_PLUS_17_5 = 'away_+17.5';
    case HOME_MINUS_12_5 = 'home_-12.5';
    case AWAY_PLUS_12_5 = 'away_+12.5';
    case HOME_MINUS_7_5 = 'home_-7.5';
    case AWAY_PLUS_7_5 = 'away_+7.5';
    case HOME_MINUS_2_5 = 'home_-2.5';
    case AWAY_PLUS_2_5 = 'away_+2.5';
    case HOME_PLUS_2_5 = 'home_+2.5';
    case AWAY_MINUS_2_5 = 'away_-2.5';
    case HOME_PLUS_7_5 = 'home_+7.5';
    case AWAY_MINUS_7_5 = 'away_-7.5';
    case HOME_PLUS_12_5 = 'home_+12.5';
    case AWAY_MINUS_12_5 = 'away_-12.5';
    case HOME_PLUS_17_5 = 'home_+17.5';
    case AWAY_MINUS_17_5 = 'away_-17.5';
    case HOME_PLUS_22_5 = 'home_+22.5';
    case AWAY_MINUS_22_5 = 'away_-22.5';

    public function team(): string
    {
        return Str::before($this->value, '_');
    }

    public function handicapValue(): float
    {
        $handicap = Str::after($this->value, '_');
        return (float) $handicap;
    }

    public function name(): string
    {
        $team = Str::ucfirst($this->team());
        $handicap = $this->handicapValue();
        $handicapStr = $handicap > 0 ? "+{$handicap}" : $handicap;
        return "{$team} ({$handicapStr})";
    }
}
