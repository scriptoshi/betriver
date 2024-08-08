<?php

namespace App\Enums\Handball\Outcomes;

enum ExactGoalsOutcome: string
{
    case ZERO = '0';
    case ONE = '1';
    case TWO = '2';
    case THREE = '3';
    case FOUR = '4';
    case FIVE = '5';
    case SIX_OR_MORE = '6+';

    public function name(): string
    {
        return match ($this) {
            self::SIX_OR_MORE => '6 or more',
            default => $this->value,
        };
    }

    public function value(): int
    {
        return match ($this) {
            self::ZERO => 0,
            self::ONE => 1,
            self::TWO => 2,
            self::THREE => 3,
            self::FOUR => 4,
            self::FIVE => 5,
            self::SIX_OR_MORE => 6,
        };
    }
}
