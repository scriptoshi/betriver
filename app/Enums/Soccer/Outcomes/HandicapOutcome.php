<?php

namespace App\Enums\Soccer\Outcomes;

use App\Traits\Handicaps;
use Illuminate\Support\Str;

enum HandicapOutcome: string
{
    use Handicaps;
    case HOME_MINUS_3 = 'home_-3';
    case DRAW_MINUS_3 = 'draw_-3';
    case AWAY_MINUS_3 = 'away_-3';
    case HOME_MINUS_2 = 'home_-2';
    case DRAW_MINUS_2 = 'draw_-2';
    case AWAY_MINUS_2 = 'away_-2';
    case HOME_MINUS_1 = 'home_-1';
    case DRAW_MINUS_1 = 'draw_-1';
    case AWAY_MINUS_1 = 'away_-1';
    case HOME_PLUS_1 = 'home_+1';
    case DRAW_PLUS_1 = 'draw_+1';
    case AWAY_PLUS_1 = 'away_+1';
    case HOME_PLUS_2 = 'home_+2';
    case DRAW_PLUS_2 = 'draw_+2';
    case AWAY_PLUS_2 = 'away_+2';
    case HOME_PLUS_3 = 'home_+3';
    case DRAW_PLUS_3 = 'draw_+3';
    case AWAY_PLUS_3 = 'away_+3';

    public function result(): string
    {
        return Str::before($this->value, '_');
    }

    public function handicapValue(): int
    {
        $handicap = Str::after($this->value, '_');
        return $handicap === '0' ? 0 : (int) $handicap;
    }

    public function name(): string
    {
        $result = Str::ucfirst($this->result());
        $handicap = $this->handicapValue();
        $handicapStr = $handicap > 0 ? "+{$handicap}" : $handicap;
        if ($handicap == 'Draw') return 'Draw';
        return formatName("{$result} ({$handicapStr})");
    }

    public static function getHandicapGroup(float $value): array
    {
        $homeHandicap = $value;
        $awayHandicap = -$value;

        return [
            self::from("home_{$homeHandicap}"),
            self::from("away_{$awayHandicap}"),
            self::from("draw_{$homeHandicap}"),
        ];
    }

    public function id()
    {
        return str($this->value)->replace('_', ' ')->title();
    }
}
