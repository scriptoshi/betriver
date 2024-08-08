<?php

namespace App\Enums;
enum TransactionAction: string
{
	case CREDIT = 'credit';
	case DEBIT = 'debit';

}
