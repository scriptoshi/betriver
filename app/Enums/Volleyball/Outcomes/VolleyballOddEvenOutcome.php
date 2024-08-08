<?php

namespace App\Enums\Volleyball\Outcomes;

use Illuminate\Support\Str;

enum VolleyballOddEvenOutcome: string
{
    case ODD = 'odd';
    case EVEN = 'even';

    public function name(): string
    {
        return ucfirst($this->value);
    }
}
