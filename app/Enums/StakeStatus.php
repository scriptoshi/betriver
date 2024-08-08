<?php

namespace App\Enums;
enum StakeStatus: string
{
	case PENDING = 'pending';
	case PARTIAL = 'partial';
	case MATCHED = 'matched';
	case WINNER = 'winner';
	case LOSER = 'loser';
	case CANCELLED = 'cancelled';

}
