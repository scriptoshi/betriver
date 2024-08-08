<?php

namespace App\Enums\Hockey;

enum ScoreType: string
{
    case TOTAL = 'total';
    case PERIOD_1 = 'first';
    case PERIOD_2 = 'second';
    case PERIOD_3 = 'third';
    public function name()
    {
        return match ($this) {
            static::PERIOD_1 => 'First Period',
            static::PERIOD_2 => 'Second Period',
            static::PERIOD_3 => 'Third Period',
            static::TOTAL => 'Total Scores',
        };
    }
}
