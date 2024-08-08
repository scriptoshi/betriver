<?php

namespace App\Enums\Baseball\Outcomes;

enum WinningMarginOutcome: string
{
    case ONE = 'one';
    case TWO = 'two';
    case THREE = 'three';
    case FOUR = 'four';
    case FIVE_PLUS = 'five_plus';

    public function name(): string
    {
        return match ($this) {
            self::ONE => 'Win by 1',
            self::TWO => 'Win by 2',
            self::THREE => 'Win by 3',
            self::FOUR => 'Win by 4',
            self::FIVE_PLUS => 'Win by 5+',
        };
    }
}
