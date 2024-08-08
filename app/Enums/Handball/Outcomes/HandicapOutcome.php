<?php

namespace App\Enums\Handball\Outcomes;

enum HandicapOutcome: string
{
    case HOME_MINUS_85 = 'home_-8.5';
    case AWAY_PLUS_85 = 'away_+8.5';
    case HOME_MINUS_75 = 'home_-7.5';
    case AWAY_PLUS_75 = 'away_+7.5';
    case HOME_MINUS_65 = 'home_-6.5';
    case AWAY_PLUS_65 = 'away_+6.5';
    case HOME_MINUS_55 = 'home_-5.5';
    case AWAY_PLUS_55 = 'away_+5.5';
    case HOME_MINUS_45 = 'home_-4.5';
    case AWAY_PLUS_45 = 'away_+4.5';
    case HOME_MINUS_35 = 'home_-3.5';
    case AWAY_PLUS_35 = 'away_+3.5';
    case HOME_MINUS_25 = 'home_-2.5';
    case AWAY_PLUS_25 = 'away_+2.5';
    case HOME_MINUS_15 = 'home_-1.5';
    case AWAY_PLUS_15 = 'away_+1.5';
    case HOME_MINUS_05 = 'home_-0.5';
    case AWAY_PLUS_05 = 'away_+0.5';

    public function name(): string
    {
        $team = $this->team();
        $value = $this->handicapValue();
        $sign = $value > 0 ? '+' : '';
        return "{$team} ({$sign}{$value})";
    }

    public function team(): string
    {
        return str($this->value)->startsWith('home') ? '{home}' : '{away}';
    }

    public function handicapValue(): float
    {
        return (float) substr($this->value, strpos($this->value, '_') + 1);
    }

    public static function getHandicapGroup(float $value): array
    {
        $homeHandicap = -$value;
        $awayHandicap = $value;
        $homeCase = 'HOME_MINUS_' . str_replace('.', '', abs($homeHandicap));
        $awayCase = 'AWAY_PLUS_' . str_replace('.', '', $awayHandicap);
        return [
            self::from("home_{$homeHandicap}"),
            self::from("away_{$awayHandicap}"),
        ];
    }
}
