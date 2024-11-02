<?php

namespace App\Enums\Hockey\Outcomes;

enum ResultBothTeamsToScoreOutcome: string
{
    case HOME_YES = 'home_yes';
    case HOME_NO = 'home_no';
    case DRAW_YES = 'draw_yes';
    case DRAW_NO = 'draw_no';
    case AWAY_YES = 'away_yes';
    case AWAY_NO = 'away_no';

    public function result(): string
    {
        return explode('_', $this->value)[0];
    }

    public function bothTeamsScored(): bool
    {
        return explode('_', $this->value)[1] == 'yes';
    }

    public function name(): string
    {
        $result = ucfirst($this->result());
        $scored = $this->bothTeamsScored() ? 'Yes' : 'No';
        return formatName("{$result} & {$scored}");
    }
}
