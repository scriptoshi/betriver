<?php

namespace App\Enums\Hockey\Outcomes;

enum WinningMarginOutcome: string
{
    case BY_1 = 'by_1';
    case BY_2 = 'by_2';
    case BY_3 = 'by_3';
    case BY_4_OR_MORE = 'by_4_or_more';

    public function name(): string
    {
        return match ($this) {
            self::BY_1 => 'By 1 goal',
            self::BY_2 => 'By 2 goals',
            self::BY_3 => 'By 3 goals',
            self::BY_4_OR_MORE => 'By 4 or more goals',
        };
    }
}
