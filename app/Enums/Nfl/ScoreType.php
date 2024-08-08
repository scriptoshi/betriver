<?php

namespace App\Enums\Nfl;

enum ScoreType: string
{
    case QUARTER_1 = 'quarter_1';
    case QUARTER_2 = 'quarter_2';
    case QUARTER_3 = 'quarter_3';
    case QUARTER_4 = 'quarter_4';
    case OVERTIME = 'overtime';
    case TOTAL = 'total';


    public function name()
    {
        return match ($this) {
            static::QUARTER_1 => 'First Quarter',
            static::QUARTER_2 => 'Second Quarter',
            static::QUARTER_3 => 'Third Quarter',
            static::QUARTER_4 => 'Fourth Quarter',
            static::OVERTIME => 'Overtime Score',
            static::TOTAL => 'Total Scores',
        };
    }

    public function getScore(object $score)
    {
        return match ($this) {
            static::QUARTER_1 => $score->quarter_1,
            static::QUARTER_2 => $score->quarter_2,
            static::QUARTER_3 => $score->quarter_3,
            static::QUARTER_4 => $score->quarter_4,
            static::OVERTIME => $score->overtime,
            static::TOTAL => $score->total,
        };
    }
}
