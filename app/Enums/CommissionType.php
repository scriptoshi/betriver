<?php

namespace App\Enums;
enum CommissionType: string
{
	case DEPOSIT = 'deposit';
	case SLIP = 'slip';
	case TICKET = 'ticket';
	case CANCELLATION = 'cancellation';

}
