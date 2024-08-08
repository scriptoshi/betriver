<?php

namespace App\Enums\Hockey\Outcomes;

enum ResultTotalGoalsOutcome: string
{
    case HOME_UNDER_35 = 'home_under_3.5';
    case HOME_OVER_35 = 'home_over_3.5';
    case DRAW_UNDER_35 = 'draw_under_3.5';
    case DRAW_OVER_35 = 'draw_over_3.5';
    case AWAY_UNDER_35 = 'away_under_3.5';
    case AWAY_OVER_35 = 'away_over_3.5';

    public function result(): string
    {
        return explode('_', $this->value)[0];
    }

    public function goalRange(): string
    {
        return explode('_', $this->value)[1] . '_' . explode('_', $this->value)[2];
    }

    public function isGoalRangeCorrect(int $totalGoals): bool
    {
        $range = $this->goalRange();
        return match ($range) {
            'under_3.5' => $totalGoals < 3.5,
            'over_3.5' => $totalGoals > 3.5,
        };
    }

    public function name(): string
    {
        $result = ucfirst($this->result());
        $range = str_replace('_', ' ', ucfirst($this->goalRange()));
        return "{$result} & {$range}";
    }
}
