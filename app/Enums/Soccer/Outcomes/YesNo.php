<?php

namespace App\Enums\Soccer\Outcomes;

use Str;

enum YesNo: string
{
    case YES = 'yes';
    case NO = 'no';

    public function name(): string
    {
        return  formatName(Str::ucfirst($this->value));
    }
}
