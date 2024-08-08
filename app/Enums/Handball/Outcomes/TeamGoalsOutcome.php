<?php

namespace App\Enums\Handball\Outcomes;

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
    case OVER_45 = 'over_4.5';
    case UNDER_45 = 'under_4.5';

    public function name(): string
    {
        return str_replace('_', ' ', ucfirst($this->value));
    }

    public function type(): string
    {
        return explode('_', $this->value)[0];
    }

    public function threshold(): float
    {
        return (float) explode('_', $this->value)[1];
    }
}
