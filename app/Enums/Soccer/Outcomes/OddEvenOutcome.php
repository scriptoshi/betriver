<?php

namespace App\Enums\Soccer\Outcomes;

use Illuminate\Support\Str;

enum OddEvenOutcome: string
{
    case ODD = 'odd';
    case EVEN = 'even';

    public function name(): string
    {
        return formatName(Str::ucfirst($this->value));
    }
}
