<?php

namespace App\Enums\Soccer\Outcomes;

use App\Traits\Overunders;
use Illuminate\Support\Str;

enum GoalsOverUnderOutcome: string
{
    use Overunders;
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
    case OVER_55 = 'over_5.5';
    case UNDER_55 = 'under_5.5';
    case OVER_65 = 'over_6.5';
    case UNDER_65 = 'under_6.5';

    public function type(): string
    {
        return Str::before($this->value, '_');
    }

    public function threshold(): float
    {
        return (float) Str::after($this->value, '_');
    }

    public function name(): string
    {
        return formatName(ucfirst($this->type()) . ' ' . $this->threshold());
    }

    public function id()
    {
        return str($this->value)->replace('_', ' ')->title();
    }
}
