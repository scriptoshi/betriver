<?php

namespace App\Enums;

enum PersonalProofOfIdentityType: string
{
    case IDCARD = 'idcard';
    case PASSPORT = 'passport';
    case LICENCE = 'licence';

    public function name()
    {
        return match ($this) {
            static::IDCARD => __('Identity card'),
            static::PASSPORT => __('Travel Passport'),
            static::LICENCE => __('Driver Licence'),
        };
    }

    /**
     * Get an array of  names.
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
