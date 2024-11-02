<?php

namespace App\Enums\Handball\Outcomes;

enum ResultBothTeamsToScoreOutcome: string
{
    case HOME_YES = 'home_yes';
    case HOME_NO = 'home_no';
    case DRAW_YES = 'draw_yes';
    case DRAW_NO = 'draw_no';
    case AWAY_YES = 'away_yes';
    case AWAY_NO = 'away_no';

    public function name(): string
    {
        $parts = explode('_', $this->value);
        $result = ucfirst($parts[0]);
        $bothScore = $parts[1] == 'yes' ? 'Yes' : 'No';
        return formatName("{$result} & {$bothScore}");
    }

    public function result(): string
    {
        return explode('_', $this->value)[0];
    }

    public function bothTeamsScore(): bool
    {
        return explode('_', $this->value)[1] == 'yes';
    }
}
