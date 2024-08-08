<?php

namespace App\Enums\Afl\Outcomes;

use Illuminate\Support\Str;

enum AFLTeamTotalsOutcome: string
{
    case OVER_35 = 'over_35';
    case UNDER_35 = 'under_35';
    case OVER_45 = 'over_45';
    case UNDER_45 = 'under_45';
    case OVER_55 = 'over_55';
    case UNDER_55 = 'under_55';
    case OVER_65 = 'over_65';
    case UNDER_65 = 'under_65';
    case OVER_75 = 'over_75';
    case UNDER_75 = 'under_75';
    case OVER_85 = 'over_85';
    case UNDER_85 = 'under_85';
    case OVER_95 = 'over_95';
    case UNDER_95 = 'under_95';
    // Add more cases as needed for different total points lines

    public function type(): string
    {
        return Str::before($this->value, '_');
    }

    public function value(): int
    {
        return (int) Str::after($this->value, '_');
    }

    public function name(): string
    {
        $type = Str::ucfirst($this->type());
        $value = $this->value();
        return "{$type} {$value}";
    }

    public static function getLineGroup(int $value): array
    {
        $overValue = "over_{$value}";
        $underValue = "under_{$value}";

        return [
            self::from($overValue),
            self::from($underValue),
        ];
    }
}
