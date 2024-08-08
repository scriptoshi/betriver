<?php

namespace App\Enums\Baseball\Outcomes;

enum HandicapOutcome: string
{
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
        return explode('_', $this->value)[0];
    }

    public function handicapValue(): float
    {
        return (float) explode('_', $this->value)[1];
    }

    public function name(): string
    {
        $team = ucfirst($this->team());
        $handicap = $this->handicapValue();
        $handicapStr = $handicap > 0 ? "+{$handicap}" : $handicap;
        return "{$team} ({$handicapStr})";
    }
}
