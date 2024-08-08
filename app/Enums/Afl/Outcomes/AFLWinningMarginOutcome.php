<?php

namespace App\Enums\Afl\Outcomes;

use Illuminate\Support\Str;

enum AFLWinningMarginOutcome: string
{
    case WIN_1_TO_9 = '1-9';
    case WIN_10_TO_19 = '10-19';
    case WIN_20_TO_29 = '20-29';
    case WIN_30_TO_39 = '30-39';
    case WIN_40_TO_49 = '40-49';
    case WIN_50_TO_59 = '50-59';
    case WIN_60_TO_69 = '60-69';
    case WIN_70_TO_79 = '70-79';
    case WIN_80_PLUS = '80+';

    public function name(): string
    {
        return match ($this) {
            self::WIN_80_PLUS => "80+",
            default => $this->value . " Points",
        };
    }

    public function minMargin(): int
    {
        return match ($this) {
            self::WIN_80_PLUS => 80,
            default => (int) Str::before($this->value, '-'),
        };
    }

    public function maxMargin(): int
    {
        return match ($this) {
            self::WIN_80_PLUS => PHP_INT_MAX,
            default => (int) Str::after($this->value, '-'),
        };
    }
}
