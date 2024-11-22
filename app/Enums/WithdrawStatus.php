<?php

namespace Scriptoshi\Payments\Enums;

enum PayoutStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case REVIEW = 'review';
    case REJECTED = 'rejected';
    case APPROVED = 'approved';
    case PROCESSING = 'processing';
    case FAILED = 'failed';
    case COMPLETE = 'complete';
    case CANCELLED = 'cancelled';
    case REVERSED = 'reversed';

    /**
     * return transactions that affect a users Quota
     */
    public static function quotaStatus()
    {
        return [
            static::PROCESSING,
            static::CONFIRMED,
            static::APPROVED,
            static::REVIEW,
            static::COMPLETE
        ];
    }

    /**
     * statuses that can be reverted
     */
    public static function canRevert()
    {
        return [
            static::REVIEW,
            static::PENDING,
        ];
    }
    /**
     * Get the human-readable name of the payout status.
     *
     * @return string
     */
    public function name(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::CONFIRMED => 'Confirmed',
            self::REVIEW => 'Under Review',
            self::REJECTED => 'Rejected',
            self::APPROVED => 'Approved',
            self::PROCESSING => 'Processing',
            self::FAILED => 'Failed',
            self::COMPLETE => 'Complete',
            self::CANCELLED => 'Cancelled',
            self::REVERSED => 'Reversed',
        };
    }

    /**
     * Get an array of all payout status names.
     *
     * @return array<string, array<string, string>>
     */
    public static function getNames(): array
    {
        return array_reduce(self::cases(), function ($carry, $case) {
            $carry[$case->value] = [
                'label' => $case->name(),
                'value' => $case->value
            ];
            return $carry;
        }, []);
    }
}
