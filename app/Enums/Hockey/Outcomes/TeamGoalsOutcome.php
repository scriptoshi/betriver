<?php

namespace App\Enums\Hockey\Outcomes;

use Str;

enum TeamGoalsOutcome: string
{
    case OVER_05 = 'over_0.5';
    case UNDER_05 = 'under_0.5';
    case OVER_15 = 'over_1.5';
    case UNDER_15 = 'under_1.5';
    case OVER_25 = 'over_2.5';
    case UNDER_25 = 'under_2.5';
    case OVER_35 = 'over_3.5';
    case UNDER_35 = 'under_3.5';

    public function type(): string
    {
        return Str::before($this->value, '_');
    }

    public function threshold(): float
    {
        return (float) Str::after($this->value, '_');
    }

    public function isCorrect(int $goals): bool
    {
        return match ($this->type()) {
            'over' => $goals > $this->threshold(),
            'under' => $goals < $this->threshold(),
        };
    }

    public function name(): string
    {
        return str_replace('_', ' ', Str::title($this->value));
    }
}
