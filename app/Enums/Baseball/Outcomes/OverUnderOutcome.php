<?php

namespace App\Enums\Baseball\Outcomes;

enum OverUnderOutcome: string
{
    case OVER_6_5 = 'over_6.5';
    case UNDER_6_5 = 'under_6.5';
    case OVER_7_5 = 'over_7.5';
    case UNDER_7_5 = 'under_7.5';
    case OVER_8_5 = 'over_8.5';
    case UNDER_8_5 = 'under_8.5';
    case OVER_9_5 = 'over_9.5';
    case UNDER_9_5 = 'under_9.5';
    case OVER_10_5 = 'over_10.5';
    case UNDER_10_5 = 'under_10.5';

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
