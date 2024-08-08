<?php

namespace App\Enums;
enum TransactionType: string
{
	case WITHDRAW = 'withdraw';
	case TRANSFER = 'transfer';
	case DEPOSIT = 'deposit';
	case WIN = 'win';
	case BET = 'bet';
	case PAYOUT = 'payout';

}
