<?php

namespace App\Enums;

enum StakeType: string
{

    case BACK = 'back';
    case LAY = 'lay';

    public function name()
    {
        return match ($this) {
            static::BACK => 'Buy',
            static::LAY => 'Sell',
        };
    }
}
