<?php

namespace App\Enums\Afl\Outcomes;

use Illuminate\Support\Str;

enum AFLHighestScoringOutcome: string
{
    case FIRST_HALF = 'first_half';
    case SECOND_HALF = 'second_half';
    case FIRST_QUARTER = 'first_quarter';
    case SECOND_QUARTER = 'second_quarter';
    case THIRD_QUARTER = 'third_quarter';
    case FOURTH_QUARTER = 'fourth_quarter';
    case EQUAL = 'equal';

    public function name(): string
    {
        return match ($this) {
            self::FIRST_HALF => '1st Half',
            self::SECOND_HALF => '2nd Half',
            self::FIRST_QUARTER => '1st Quarter',
            self::SECOND_QUARTER => '2nd Quarter',
            self::THIRD_QUARTER => '3rd Quarter',
            self::FOURTH_QUARTER => '4th Quarter',
            self::EQUAL => 'Equal',
        };
    }
}
