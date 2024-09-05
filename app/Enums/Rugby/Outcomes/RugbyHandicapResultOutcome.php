<?php

namespace App\Enums\Rugby\Outcomes;

use Google\Service\PolyService\Format;
use Illuminate\Support\Str;

enum RugbyHandicapResultOutcome: string
{
    case HOME_MINUS_20 = 'home_-20';
    case DRAW_MINUS_20 = 'draw_-20';
    case AWAY_MINUS_20 = 'away_-20';
    case HOME_MINUS_10 = 'home_-10';
    case DRAW_MINUS_10 = 'draw_-10';
    case AWAY_MINUS_10 = 'away_-10';
    case HOME_PLUS_10 = 'home_+10';
    case DRAW_PLUS_10 = 'draw_+10';
    case AWAY_PLUS_10 = 'away_+10';
    case HOME_PLUS_20 = 'home_+20';
    case DRAW_PLUS_20 = 'draw_+20';
    case AWAY_PLUS_20 = 'away_+20';

    public function result(): string
    {
        return Str::before($this->value, '_');
    }

    public function handicapValue(): int
    {
        return (int) Str::after($this->value, '_');
    }

    public function name(): string
    {
        $result = Str::ucfirst($this->result());
        $handicap = $this->handicapValue();
        $handicapStr = $handicap > 0 ? "+{$handicap}" : $handicap;
        return formatName("{$result} ({$handicapStr})");
    }
}
