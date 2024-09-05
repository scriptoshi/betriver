<?php

namespace App\Enums;

enum WhitelistStatus: string
{
    case PENDING = 'pending';
    case REVIEW = 'review';
    case PROCESSING = 'processing';
    case REJECTED = 'rejected';
    case APPROVED = 'approved';
    case CANCELLED = 'cancelled';
}
