<?php

namespace App\Enums\Basketball\Outcomes;

use App\Traits\Overunders;

enum OverUnderOutcome: string
{
    use Overunders;
    case OVER_180 = 'over_180';
    case UNDER_180 = 'under_180';
    case OVER_185 = 'over_185';
    case UNDER_185 = 'under_185';
    case OVER_190 = 'over_190';
    case UNDER_190 = 'under_190';
    case OVER_195 = 'over_195';
    case UNDER_195 = 'under_195';
    case OVER_200 = 'over_200';
    case UNDER_200 = 'under_200';
    case OVER_205 = 'over_205';
    case UNDER_205 = 'under_205';

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
