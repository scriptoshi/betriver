<?php

namespace App\Enums;

enum DepositStatus: string
{
    case PENDING = 'pending';
    case REVIEW = 'review';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';
    case APPROVED = 'approved';
    case PROCESSING = 'processing';
    case FAILED = 'failed';
    case COMPLETE = 'complete';
    case REFUNDED = 'refunded';
    case REVERSED = 'reversed';

    /**
     * return transactions that affect a users Quota
     */
    public static function quotaStatus()
    {
        return [
            DepositStatus::PROCESSING,
            DepositStatus::PENDING,
            DepositStatus::APPROVED,
            DepositStatus::REVIEW,
            DepositStatus::COMPLETE
        ];
    }


    /**
     * Get the human-readable name of the deposit status.
     *
     * @return string
     */
    public function getName(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::REVIEW => 'Under Review',
            self::REJECTED => 'Rejected',
            self::CANCELLED => 'Cancelled',
            self::APPROVED => 'Approved',
            self::PROCESSING => 'Processing',
            self::FAILED => 'Failed',
            self::COMPLETE => 'Complete',
            self::REFUNDED => 'Refunded',
            self::REVERSED => 'Reversed',
        };
    }

    /**
     * Get an array of all deposit status names.
     *
     * @return array<string, array<string, string>>
     */
    public static function getNames(): array
    {
        return array_reduce(self::cases(), function ($carry, $case) {
            $carry[$case->value] = [
                'name' => $case->getName(),
                'value' => $case->value
            ];
            return $carry;
        }, []);
    }
}
