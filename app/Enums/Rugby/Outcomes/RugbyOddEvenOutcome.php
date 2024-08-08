<?php

namespace App\Enums\Rugby\Outcomes;

enum RugbyOddEvenOutcome: string
{
    case ODD = 'odd';
    case EVEN = 'even';

    public function name(): string
    {
        return ucfirst($this->value);
    }
}
