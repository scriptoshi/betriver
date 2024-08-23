<?php

namespace App\Enums\MMA\Outcomes;

use Illuminate\Support\Str;

enum MMARoundBettingOutcome: string
{
    case ROUND_1 = 'round_1';
    case ROUND_2 = 'round_2';
    case ROUND_3 = 'round_3';
    case ROUND_4 = 'round_4';
    case ROUND_5 = 'round_5';
    case HOME_ROUND_1 = 'home_round_1';
    case HOME_ROUND_2 = 'home_round_2';
    case HOME_ROUND_3 = 'home_round_3';
    case HOME_ROUND_4 = 'home_round_4';
    case HOME_ROUND_5 = 'home_round_5';
    case AWAY_ROUND_1 = 'away_round_1';
    case AWAY_ROUND_2 = 'away_round_2';
    case AWAY_ROUND_3 = 'away_round_3';
    case AWAY_ROUND_4 = 'away_round_4';
    case AWAY_ROUND_5 = 'away_round_5';

    public function round(): ?int
    {
        if (Str::contains($this->value, 'round_')) {
            return (int) Str::after($this->value, 'round_');
        }
        return null;
    }

    public function player(): ?string
    {
        if (Str::startsWith($this->value, 'home_')) {
            return 'home';
        } elseif (Str::startsWith($this->value, 'away_')) {
            return 'away';
        }
        return null;
    }

    public function name(): string
    {
        $name  = value(function () {
            if ($this->player()) {
                $player = $this->player();
                $round = $this->round();
                return "{$player} Round {$round}";
            } else {
                return "Round " . $this->round();
            }
        });
        return formatName($name);
    }
}
