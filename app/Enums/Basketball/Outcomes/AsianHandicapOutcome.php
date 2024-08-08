<?php

namespace App\Enums\Basketball\Outcomes;

use Illuminate\Support\Str;

enum AsianHandicapOutcome: string
{
    case HOME_MINUS_15 = 'home_-15';
    case AWAY_PLUS_15 = 'away_+15';
    case HOME_MINUS_10 = 'home_-10';
    case AWAY_PLUS_10 = 'away_+10';
    case HOME_MINUS_5 = 'home_-5';
    case AWAY_PLUS_5 = 'away_+5';
    case HOME_PLUS_5 = 'home_+5';
    case AWAY_MINUS_5 = 'away_-5';
    case HOME_PLUS_10 = 'home_+10';
    case AWAY_MINUS_10 = 'away_-10';
    case HOME_PLUS_15 = 'home_+15';
    case AWAY_MINUS_15 = 'away_-15';

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

    public static function getHandicapGroup(float $value): array
    {
        $homeHandicap = $value;
        $awayHandicap = -$value;
        return [
            self::from("home_{$homeHandicap}"),
            self::from("away_{$awayHandicap}"),
        ];
    }
}
