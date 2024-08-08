<?php

namespace App\Enums;
enum WithdrawStatus: string
{
	case PENDING = 'pending';
	case REVIEW = 'review';
	case REJECTED = 'rejected';
	case APPROVED = 'approved';
	case PROCESSING = 'processing';
	case FAILED = 'failed';
	case COMPLETE = 'complete';

}
