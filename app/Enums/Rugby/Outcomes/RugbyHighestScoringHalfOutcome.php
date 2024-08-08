<?php

namespace App\Enums\Rugby\Outcomes;

enum RugbyHighestScoringHalfOutcome: string
{
    case FIRST_HALF = 'first_half';
    case SECOND_HALF = 'second_half';
    case EQUAL = 'equal';

    public function name(): string
    {
        return match ($this) {
            self::FIRST_HALF => '1st Half',
            self::SECOND_HALF => '2nd Half',
            self::EQUAL => 'Equal',
        };
    }
}
