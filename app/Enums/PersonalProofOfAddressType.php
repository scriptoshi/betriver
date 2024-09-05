<?php

namespace App\Enums;

enum PersonalProofOfAddressType: string
{
    case UTILITY_BILL = 'utility_bill';
    case BANK_STATEMENT = 'bank_statement';
    case OTHER = 'other';

    public function name()
    {
        return match ($this) {
            static::UTILITY_BILL => __('Utility Bill'),
            static::BANK_STATEMENT => __('Bank Statement'),
            static::OTHER => __('Other'),
        };
    }

    /**
     * Get an array of all  names.
     *
     * @return array<string, array>
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
