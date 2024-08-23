<?php

namespace App\Enums;

enum StakeStatus: string
{
    case PENDING = 'pending';
    case PARTIAL = 'partial';
    case MATCHED = 'matched';
    case WINNER = 'winner';
    case LOST = 'lost';
    case CANCELLED = 'cancelled';
    case GAME_CANCELLED = 'game_cancelled';
    case REFUNDED = 'refunded';
    case TRADE_OUT = 'trade_out';
    case TRADED_OUT = 'traded_out';
}
