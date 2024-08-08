<?php

namespace App\Enums\Afl\Outcomes;

use Illuminate\Support\Str;

enum AFLAsianHandicapOutcome: string
{
    case HOME_MINUS_205 = 'home_-205';
    case AWAY_PLUS_205 = 'away_+205';
    case HOME_MINUS_155 = 'home_-155';
    case AWAY_PLUS_155 = 'away_+155';
    case HOME_MINUS_105 = 'home_-105';
    case AWAY_PLUS_105 = 'away_+105';
    case HOME_MINUS_55 = 'home_-55';
    case AWAY_PLUS_55 = 'away_+55';
    case HOME_MINUS_5 = 'home_-5';
    case AWAY_PLUS_5 = 'away_+5';
    case HOME_PLUS_5 = 'home_+5';
    case AWAY_MINUS_5 = 'away_-5';
    case HOME_PLUS_55 = 'home_+55';
    case AWAY_MINUS_55 = 'away_-55';
    case HOME_PLUS_105 = 'home_+105';
    case AWAY_MINUS_105 = 'away_-105';
    case HOME_PLUS_155 = 'home_+155';
    case AWAY_MINUS_155 = 'away_-155';
    case HOME_PLUS_205 = 'home_+205';
    case AWAY_MINUS_205 = 'away_-205';

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
        return formatName("{$team} ({$handicapStr})");
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
