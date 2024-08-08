<?php

namespace App\Enums\Afl\Outcomes;

use Illuminate\Support\Str;

enum AFLTotalScoresOutcome: string
{
    case OVER_5 = 'over_5';
    case UNDER_5 = 'under_5';
    case OVER_10 = 'over_10';
    case UNDER_10 = 'under_10';
    case OVER_15 = 'over_15';
    case UNDER_15 = 'under_15';
    case OVER_20 = 'over_20';
    case UNDER_20 = 'under_20';
    case OVER_25 = 'over_25';
    case UNDER_25 = 'under_25';
    case OVER_30 = 'over_30';
    case UNDER_30 = 'under_30';
    // Add more cases as needed

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

    public static function fromLine(string $type, int $value): self
    {
        $key = "{$type}_{$value}";
        return self::from($key);
    }
}
