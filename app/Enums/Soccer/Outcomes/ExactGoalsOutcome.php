<?php

namespace App\Enums\Soccer\Outcomes;

enum ExactGoalsOutcome: string
{
    case ZERO = '0';
    case ONE = '1';
    case TWO = '2';
    case THREE = '3';
    case THREE_PLUS = 'more_3';
    case FOUR = '4';
    case FIVE = '5';
    case SIX_PLUS = 'more_6';

    public function name(): string
    {
        $name = match ($this) {
            self::ZERO => 'No Goals',
            self::ONE => '1 Goal',
            self::THREE_PLUS => '3 or more goals',
            self::SIX_PLUS => '6 or more goals',
            default => "{$this->value} Goals",
        };
        return formatName($name);
    }

    public static function threeOrLess(): array
    {
        return [
            self::ZERO,
            self::ONE,
            self::TWO,
            self::THREE_PLUS,
        ];
    }

    public static function sixOrLess(): array
    {
        return [
            self::ZERO,
            self::ONE,
            self::TWO,
            self::THREE,
            self::FOUR,
            self::FIVE,
            self::SIX_PLUS,
        ];
    }

    function id()
    {
        return $this->value;
    }
}
