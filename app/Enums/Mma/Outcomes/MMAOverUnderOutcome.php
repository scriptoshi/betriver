<?php

namespace App\Enums\Mma\Outcomes;

use App\Traits\Overunders;
use Illuminate\Support\Str;

enum MMAOverUnderOutcome: string
{

    use Overunders;
    case OVER_15 = 'over_1.5';
    case UNDER_15 = 'under_1.5';
    case OVER_25 = 'over_2.5';
    case UNDER_25 = 'under_2.5';
    case OVER_35 = 'over_3.5';
    case UNDER_35 = 'under_3.5';
    case OVER_45 = 'over_4.5';
    case UNDER_45 = 'under_4.5';

    public function type(): string
    {
        return Str::before($this->value, '_');
    }


    public function lineValue(): ?float
    {
        return (float) Str::after($this->value, '_');
    }

    public function name(): string
    {
        $type = Str::ucfirst($this->type());
        $line = $this->lineValue();
        return "{$type} {$line}";
    }
}
