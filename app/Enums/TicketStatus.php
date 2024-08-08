<?php

namespace App\Enums;
enum TicketStatus: string
{
	case PENDING = 'pending';
	case WINNER = 'winner';
	case LOSER = 'loser';
	case CANCELLED = 'cancelled';

}
