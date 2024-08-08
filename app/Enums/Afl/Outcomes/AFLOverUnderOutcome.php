<?php

namespace App\Enums\Afl\Outcomes;

use Illuminate\Support\Str;


enum AFLOverUnderOutcome: string
{
    case OVER_145 = 'over_145';
    case UNDER_145 = 'under_145';
    case OVER_155 = 'over_155';
    case UNDER_155 = 'under_155';
    case OVER_165 = 'over_165';
    case UNDER_165 = 'under_165';
    case OVER_175 = 'over_175';
    case UNDER_175 = 'under_175';
    case OVER_185 = 'over_185';
    case UNDER_185 = 'under_185';
    case OVER_195 = 'over_195';
    case UNDER_195 = 'under_195';
    // Add more cases as needed for different total score lines

    public function type(): string
    {
        return Str::before($this->value, '_');
    }

    public function lineValue(): float
    {
        return (float) Str::after($this->value, '_');
    }

    public function name(): string
    {
        $type = Str::ucfirst($this->type());
        $line = $this->lineValue();
        return "{$type} {$line}";
    }

    public static function getLineGroup(float $value): array
    {
        $overValue = "over_{$value}";
        $underValue = "under_{$value}";

        return [
            self::from($overValue),
            self::from($underValue),
        ];
    }
}
