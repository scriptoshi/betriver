<?php

namespace App\Enums\Soccer\Outcomes;

enum HighestScoringHalfOutcome: string
{
    case FIRST = 'first';
    case SECOND = 'second';
    case EQUAL = 'equal';

    public function name(): string
    {
        return formatName(match ($this) {
            self::FIRST => '1st Half',
            self::SECOND => '2nd Half',
            self::EQUAL => 'Equal',
        });
    }
}
