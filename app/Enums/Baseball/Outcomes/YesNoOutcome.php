<?php

namespace App\Enums\Baseball\Outcomes;

enum YesNoOutcome: string
{
    case YES = 'yes';
    case NO = 'no';

    public function name(): string
    {
        return ucfirst($this->value);
    }
}
