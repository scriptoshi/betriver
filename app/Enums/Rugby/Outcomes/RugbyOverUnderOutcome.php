<?php

namespace App\Enums\Rugby\Outcomes;

use App\Traits\Overunders;
use Illuminate\Support\Str;

enum RugbyOverUnderOutcome: string
{
    use Overunders;
    case OVER_35_5 = 'over_35.5';
    case UNDER_35_5 = 'under_35.5';
    case OVER_40_5 = 'over_40.5';
    case UNDER_40_5 = 'under_40.5';
    case OVER_45_5 = 'over_45.5';
    case UNDER_45_5 = 'under_45.5';
    case OVER_50_5 = 'over_50.5';
    case UNDER_50_5 = 'under_50.5';
    case OVER_55_5 = 'over_55.5';
    case UNDER_55_5 = 'under_55.5';

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
        return Str::ucfirst($this->type()) . ' ' . $this->threshold();
    }
}
