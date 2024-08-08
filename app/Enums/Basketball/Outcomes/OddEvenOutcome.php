<?php

namespace App\Enums\Basketball\Outcomes;

enum OddEvenOutcome: string
{
    case ODD = 'odd';
    case EVEN = 'even';

    public function name(): string
    {
        return ucfirst($this->value);
    }
}
