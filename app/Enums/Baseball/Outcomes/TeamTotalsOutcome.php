<?php

namespace App\Enums\Baseball\Outcomes;

enum TeamTotalsOutcome: string
{
    case OVER_4_5 = 'over_4.5';
    case UNDER_4_5 = 'under_4.5';
    case OVER_5_5 = 'over_5.5';
    case UNDER_5_5 = 'under_5.5';
    case OVER_6_5 = 'over_6.5';
    case UNDER_6_5 = 'under_6.5';
    case OVER_7_5 = 'over_7.5';
    case UNDER_7_5 = 'under_7.5';
    case OVER_8_5 = 'over_8.5';
    case UNDER_8_5 = 'under_8.5';
    case OVER_9_5 = 'over_9.5';
    case UNDER_9_5 = 'under_9.5';

    public function type(): string
    {
        return explode('_', $this->value)[0];
    }

    public function threshold(): float
    {
        return (float) explode('_', $this->value)[1];
    }

    public function name(): string
    {
        return ucfirst($this->value);
    }
}
