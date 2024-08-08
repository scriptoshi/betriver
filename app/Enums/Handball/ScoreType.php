<?php

namespace App\Enums\Handball;

enum ScoreType: string
{
    case TOTAL = 'total';
    case PERIOD_1 = 'firsthalf';
    case PERIOD_2 = 'secondhalf';
    public function name()
    {
        return match ($this) {
            static::PERIOD_1 => 'First Period',
            static::PERIOD_2 => 'Second Period',
            static::TOTAL => 'Total Scores',
        };
    }
}
