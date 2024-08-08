<?php

namespace App\Enums\Rugby;

enum ScoreType: string
{
    case TOTAL = 'total';
    case PERIOD_1 = 'first';
    case PERIOD_2 = 'second';
    case OVERTIME = 'overtime';
    case SECOND_OVERTIME = 'second_overtime';

    public function name()
    {
        return match ($this) {
            static::PERIOD_1 => 'First Period',
            static::PERIOD_2 => 'Second Period',
            static::OVERTIME => 'Overtime',
            static::SECOND_OVERTIME => 'Second Overtime',
            static::TOTAL => 'Total Scores',
        };
    }
}
