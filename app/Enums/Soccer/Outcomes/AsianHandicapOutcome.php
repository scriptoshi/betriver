<?php

namespace App\Enums\Soccer\Outcomes;

use Illuminate\Support\Str;

enum AsianHandicapOutcome: string
{
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

    public function id()
    {
        return str($this->value)->replace('_', ' ')->title();
    }
}
