<?php

namespace App\Enums\Soccer\Outcomes;

enum CorrectScoreOutcome: string
{
    case SCORE_0_0 = '0-0';
    case SCORE_1_0 = '1-0';
    case SCORE_0_1 = '0-1';
    case SCORE_1_1 = '1-1';
    case SCORE_2_0 = '2-0';
    case SCORE_0_2 = '0-2';
    case SCORE_2_1 = '2-1';
    case SCORE_1_2 = '1-2';
    case SCORE_2_2 = '2-2';
    case SCORE_3_0 = '3-0';
    case SCORE_0_3 = '0-3';
    case SCORE_3_1 = '3-1';
    case SCORE_1_3 = '1-3';
    case SCORE_3_2 = '3-2';
    case SCORE_2_3 = '2-3';
    case SCORE_3_3 = '3-3';

    public function name(): string
    {
        return formatName($this->value);
    }

    public function homeGoals(): int
    {
        return (int) explode('-', $this->value)[0];
    }

    public function awayGoals(): int
    {
        return (int) explode('-', $this->value)[1];
    }

    public function id()
    {
        return str($this->value)->replace('-', ':');
    }
}
