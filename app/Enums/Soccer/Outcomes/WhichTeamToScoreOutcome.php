<?php

namespace App\Enums\Soccer\Outcomes;

enum WhichTeamToScoreOutcome: string
{
    case HOME = 'home';
    case AWAY = 'away';
    case BOTH = 'both';
    case NONE = 'none';

    public function name(): string
    {
        return formatName(match ($this) {
            self::HOME => 'Home Team',
            self::AWAY => 'Away Team',
            self::BOTH => 'Both Teams',
            self::NONE => 'Neither Team',
        });
    }
}
