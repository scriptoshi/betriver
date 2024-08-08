<?php

namespace App\Enums\Volleyball\Outcomes;

use Illuminate\Support\Str;

enum VolleyballOverUnderOutcome: string
{
    case OVER_3 = 'over_3';
    case UNDER_3 = 'under_3';
    case OVER_4 = 'over_4';
    case UNDER_4 = 'under_4';
    case OVER_5 = 'over_5';
    case UNDER_5 = 'under_5';

    public function type(): string
    {
        return Str::before($this->value, '_');
    }

    public function value(): float
    {
        return (float) Str::after($this->value, '_');
    }

    public function name(): string
    {
        return ucfirst($this->type()) . ' ' . $this->value();
    }
}
