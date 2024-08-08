<?php

namespace App\Enums\Afl\Outcomes;

use Illuminate\Support\Str;


enum AFLOddEvenOutcome: string
{
    case ODD = 'odd';
    case EVEN = 'even';

    public function name(): string
    {
        return Str::ucfirst($this->value);
    }
}
