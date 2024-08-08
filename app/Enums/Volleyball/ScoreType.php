<?php

namespace App\Enums\Volleyball;

enum ScoreType: string
{
    case TOTAL = 'total';
    case PERIOD_1 = 'first';
    case PERIOD_2 = 'second';
    case PERIOD_3 = 'third';
    case PERIOD_4 = 'fourth';
    case PERIOD_5 = 'fifth';


    public function name()
    {
        return match ($this) {
            static::PERIOD_1 => 'First Period',
            static::PERIOD_2 => 'Second Period',
            static::PERIOD_3 => 'Third Period',
            static::PERIOD_4 => 'Fourth Period',
            static::PERIOD_5 => 'Fifth Period',
            static::TOTAL => 'Total Goals',
        };
    }
}
