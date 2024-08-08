<?php

namespace App\Enums\Baseball;

enum ScoreType: string
{
    case TOTAL = 'total';
    case HITS = 'hits';
    case ERRORS = 'errors';
    case INNINGS_1 = 'innings_1';
    case INNINGS_2 = 'innings_2';
    case INNINGS_3 = 'innings_3';
    case INNINGS_4 = 'innings_4';
    case INNINGS_5 = 'innings_5';
    case INNINGS_6 = 'innings_6';
    case INNINGS_7 = 'innings_7';
    case INNINGS_8 = 'innings_8';
    case INNINGS_9 = 'innings_9';
    case INNINGS_EXTRA = 'innings_extra';

    public function name()
    {
        return match ($this) {
            static::TOTAL => 'Total Scores',
            static::HITS => 'Total Hits',
            static::ERRORS => 'Errors',
            static::INNINGS_1 => 'First inning',
            static::INNINGS_2 => 'Second inning',
            static::INNINGS_3 => 'Third inning',
            static::INNINGS_4 => 'Fourth inning',
            static::INNINGS_5 => 'Fifth inning',
            static::INNINGS_6 => 'Sixth inning',
            static::INNINGS_7 => 'Seventh inning',
            static::INNINGS_8 => 'Eighth inning',
            static::INNINGS_9 => 'Nineth inning',
            static::INNINGS_EXTRA => 'Extra inning'
        };
    }

    public function getScore($scores)
    {
        $innings = (array)$scores->innings;
        return match ($this) {
            static::TOTAL => $scores->total,
            static::HITS => $scores->hits,
            static::ERRORS => $scores->errors,
            static::INNINGS_1 => $innings[1] ?? null,
            static::INNINGS_2 =>  $innings[2] ?? null,
            static::INNINGS_3 =>  $innings[3] ?? null,
            static::INNINGS_4 =>  $innings[4] ?? null,
            static::INNINGS_5 =>  $innings[5] ?? null,
            static::INNINGS_6 =>  $innings[6] ?? null,
            static::INNINGS_7 => $innings[7] ?? null,
            static::INNINGS_8 =>  $innings[8] ?? null,
            static::INNINGS_9 =>  $innings[9] ?? null,
            static::INNINGS_EXTRA =>  $innings['extra'] ?? null,
        };
    }
}
