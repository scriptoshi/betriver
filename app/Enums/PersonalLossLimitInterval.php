<?php

namespace App\Enums;

enum PersonalLossLimitInterval: string
{
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';
    case YEARLY = 'yearly';


    public function name()
    {
        return match ($this) {
            static::DAILY => __('Daily'),
            static::WEEKLY => __('Weekly'),
            static::MONTHLY => __('Monthly'),
            static::YEARLY => __('Yearly'),
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
