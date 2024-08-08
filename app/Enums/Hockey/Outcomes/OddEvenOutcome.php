<?php

namespace App\Enums\Hockey\Outcomes;

enum OddEvenOutcome: string
{
    case ODD = 'odd';
    case EVEN = 'even';

    public function name(): string
    {
        return ucfirst($this->value);
    }
}
