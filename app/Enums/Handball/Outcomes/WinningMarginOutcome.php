<?php

namespace App\Enums\Handball\Outcomes;

enum WinningMarginOutcome: string
{
    case HOME_1_2 = 'home_1-2';
    case HOME_3_4 = 'home_3-4';
    case HOME_5_6 = 'home_5-6';
    case HOME_7_8 = 'home_7-8';
    case HOME_9_PLUS = 'home_9+';
    case AWAY_1_2 = 'away_1-2';
    case AWAY_3_4 = 'away_3-4';
    case AWAY_5_6 = 'away_5-6';
    case AWAY_7_8 = 'away_7-8';
    case AWAY_9_PLUS = 'away_9+';

    public function name(): string
    {
        $parts = explode('_', $this->value);
        $team = ucfirst($parts[0]);
        $margin = str_replace('-', ' to ', $parts[1]);
        $margin = str_replace('+', ' or more', $margin);
        return formatName("{$team} by {$margin}");
    }

    public function team(): string
    {
        return explode('_', $this->value)[0];
    }

    public function minMargin(): int
    {
        $margin = explode('_', $this->value)[1];
        return (int) explode('-', $margin)[0];
    }

    public function maxMargin(): ?int
    {
        $margin = explode('_', $this->value)[1];
        if (str_contains($margin, '+')) {
            return null;
        }
        return (int) explode('-', $margin)[1];
    }
}
