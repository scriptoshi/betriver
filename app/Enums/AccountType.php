<?php

namespace App\Enums;
enum AccountType: string
{
	case WINS = 'wins';
	case LOSS = 'loss';
	case ARBITRAGE = 'arbitrage';
	case FEES = 'fees';

}
