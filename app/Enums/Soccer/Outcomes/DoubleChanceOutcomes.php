<?php

namespace App\Enums\Soccer\Outcomes;


enum DoubleChanceOutcomes: string
{
    case HOME_DRAW = 'home_draw';
    case HOME_AWAY = 'home_away';
    case DRAW_AWAY = 'draw_away';

    public function possibleOutcomes(): array
    {
        return match ($this) {
            self::HOME_DRAW => [MatchWinner::HOME, MatchWinner::DRAW],
            self::HOME_AWAY => [MatchWinner::HOME, MatchWinner::AWAY],
            self::DRAW_AWAY => [MatchWinner::DRAW, MatchWinner::AWAY],
        };
    }

    public function name()
    {
        return formatName(str($this->value)->ucfirst()->replace('_', ' or '));
    }


    public static function find($name)
    {
        $val = str($name)
            ->replace('/', '_')
            ->replace(' ', '_')
            ->lower()
            ->value();
        return static::tryFrom($val)->value;
    }
}
