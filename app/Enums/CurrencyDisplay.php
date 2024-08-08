<?php

namespace App\Enums;

enum CurrencyDisplay: string
{
    case SYMBOL = 'symbol';
    case CODE = 'code';
    case AUTO = 'auto';
}
