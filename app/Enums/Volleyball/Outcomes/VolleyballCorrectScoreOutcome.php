<?php

namespace App\Enums\Volleyball\Outcomes;

use Illuminate\Support\Str;

enum VolleyballCorrectScoreOutcome: string
{
    case HOME_3_0 = '3-0';
    case HOME_3_1 = '3-1';
    case HOME_3_2 = '3-2';
    case AWAY_0_3 = '0-3';
    case AWAY_1_3 = '1-3';
    case AWAY_2_3 = '2-3';

    public function name(): string
    {
        return $this->value;
    }

    public function homeSets(): int
    {
        return (int) Str::before($this->value, '-');
    }

    public function awaySets(): int
    {
        return (int) Str::after($this->value, '-');
    }
}
