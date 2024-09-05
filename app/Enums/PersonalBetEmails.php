<?php

namespace App\Enums;

enum PersonalBetEmails: string
{
    case SUMMARY = 'summary';
    case SETTLE = 'settle';
    case NONE = 'none';

    public function name()
    {
        return match ($this) {
            static::SUMMARY => __('Just email me one summary a day'),
            static::SETTLE => __('Email me when a market I bet on settles'),
            static::NONE => __('Please don\'t email me about settled bets')
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
