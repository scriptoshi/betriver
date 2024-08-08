<?php

namespace App\Enums;
enum TransactionStatus: string
{
	case PENDING = 'pending';
	case COMPLETE = 'complete';
	case FAILED = 'failed';
	case REJECTED = 'rejected';
	case REVIEW = 'review';

}
