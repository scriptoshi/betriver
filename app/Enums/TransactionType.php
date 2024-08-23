<?php

namespace App\Enums;

enum TransactionType: string
{
    case WITHDRAW = 'withdraw';
    case TRANSFER = 'transfer';
    case DEPOSIT = 'deposit';
    case WIN = 'win';
    case BET = 'bet';
    case BET_REFUND = 'bet_refund';
    case PAYOUT = 'payout';
    case TRADE_OUT_LIABILITY = 'tradeout_liability';
    case TRADE_OUT_PROFIT = 'tradeout_profit';
}
