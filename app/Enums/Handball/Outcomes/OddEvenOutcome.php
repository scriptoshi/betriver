<?php

namespace App\Enums\Handball\Outcomes;

enum OddEvenOutcome: string
{
    case ODD = 'odd';
    case EVEN = 'even';

    public function name(): string
    {
        return ucfirst($this->value);
    }

    public function isCorrect(int $totalGoals): bool
    {
        return match ($this) {
            self::ODD => $totalGoals % 2 !== 0,
            self::EVEN => $totalGoals % 2 === 0,
        };
    }
}
