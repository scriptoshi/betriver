<?php

namespace App\Enums\Rugby\Outcomes;

use Illuminate\Support\Str;

enum RugbyTeamTotalsOutcome: string
{
    case OVER_15_5 = 'over_15.5';
    case UNDER_15_5 = 'under_15.5';
    case OVER_18_5 = 'over_18.5';
    case UNDER_18_5 = 'under_18.5';
    case OVER_21_5 = 'over_21.5';
    case UNDER_21_5 = 'under_21.5';
    case OVER_24_5 = 'over_24.5';
    case UNDER_24_5 = 'under_24.5';
    case OVER_27_5 = 'over_27.5';
    case UNDER_27_5 = 'under_27.5';

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
        $type = Str::ucfirst($this->type());
        $value = $this->value();
        return "{$type} {$value}";
    }
}
