<?php

namespace App\Enums\Nfl\Outcomes;

use Illuminate\Support\Str;

enum NFLWinningMarginOutcome: string
{
    case WIN_1_TO_6 = '1-6';
    case WIN_7_TO_12 = '7-12';
    case WIN_13_TO_18 = '13-18';
    case WIN_19_TO_24 = '19-24';
    case WIN_25_TO_30 = '25-30';
    case WIN_31_TO_36 = '31-36';
    case WIN_37_TO_42 = '37-42';
    case WIN_43_PLUS = '43+';

    public function name(): string
    {
        return match ($this) {
            self::WIN_43_PLUS => "43+",
            default => $this->value . " Points",
        };
    }

    public function minMargin(): int
    {
        return match ($this) {
            self::WIN_43_PLUS => 43,
            default => (int) Str::before($this->value, '-'),
        };
    }

    public function maxMargin(): int
    {
        return match ($this) {
            self::WIN_43_PLUS => PHP_INT_MAX,
            default => (int) Str::after($this->value, '-'),
        };
    }
}
