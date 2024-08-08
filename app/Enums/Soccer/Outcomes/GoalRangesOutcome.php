<?php

namespace App\Enums\Soccer\Outcomes;

enum GoalRangesOutcome: string
{
    case RANGE_0_1 = '0-1';
    case RANGE_2_3 = '2-3';
    case RANGE_4_6 = '4-6';
    case RANGE_7_PLUS = '7+';

    public function name(): string
    {
        return formatName(match ($this) {
            self::RANGE_0_1 => '0-1 Goals',
            self::RANGE_2_3 => '2-3 Goals',
            self::RANGE_4_6 => '4-6 Goals',
            self::RANGE_7_PLUS => '7 or more Goals',
        });
    }

    public function isInRange(int $goals): bool
    {
        return match ($this) {
            self::RANGE_0_1 => $goals >= 0 && $goals <= 1,
            self::RANGE_2_3 => $goals >= 2 && $goals <= 3,
            self::RANGE_4_6 => $goals >= 4 && $goals <= 6,
            self::RANGE_7_PLUS => $goals >= 7,
        };
    }

    function id()
    {
        return $this->value;
    }
}
