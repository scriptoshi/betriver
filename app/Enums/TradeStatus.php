<?php

namespace App\Enums;

enum TradeStatus: string
{
    case PENDING = 'pending';
    case SETTLED = 'settled';
    case CANCELLED = 'cancelled';
}
