<?php

namespace App\Enums\Rugby\Outcomes;

enum RugbyExactGoalsOutcome: string
{
    case ZERO = '0';
    case ONE = '1';
    case TWO = '2';
    case THREE = '3';
    case FOUR = '4';
    case FIVE = '5';
    case SIX_PLUS = '6+';

    public function name(): string
    {
        return $this->value;
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
            self::SIX_PLUS => 6,
        };
    }
}
