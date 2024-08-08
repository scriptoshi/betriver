<?php

namespace App\Enums\Rugby\Outcomes;

use Illuminate\Support\Str;

enum RugbyWinningMarginOutcome: string
{
    case HOME_1_5 = 'home_1-5';
    case HOME_6_10 = 'home_6-10';
    case HOME_11_15 = 'home_11-15';
    case HOME_16_20 = 'home_16-20';
    case HOME_21_25 = 'home_21-25';
    case HOME_26_30 = 'home_26-30';
    case HOME_31_PLUS = 'home_31+';
    case AWAY_1_5 = 'away_1-5';
    case AWAY_6_10 = 'away_6-10';
    case AWAY_11_15 = 'away_11-15';
    case AWAY_16_20 = 'away_16-20';
    case AWAY_21_25 = 'away_21-25';
    case AWAY_26_30 = 'away_26-30';
    case AWAY_31_PLUS = 'away_31+';

    public function team(): string
    {
        return Str::before($this->value, '_');
    }

    public function range(): string
    {
        return Str::after($this->value, '_');
    }

    public function name(): string
    {
        $team = ucfirst($this->team());
        $range = $this->range();
        return "{$team} to win by {$range}";
    }

    public function minMargin(): int
    {
        if (Str::endsWith($this->value, '+')) {
            return (int) Str::before($this->range(), '+');
        }
        return (int) Str::before($this->range(), '-');
    }

    public function maxMargin(): int
    {
        if (Str::endsWith($this->value, '+')) {
            return PHP_INT_MAX;
        }
        return (int) Str::after($this->range(), '-');
    }
}
