<?php

namespace App\Enums\Mma;

enum ScoreType: string
{
    case JUDGE_ONE = 'judge_one';
    case JUDGE_TWO = 'judge_two';
    case JUDGE_THREE = 'judge_three';
    public function name()
    {
        return match ($this) {
            static::JUDGE_ONE => 'First Judge Scores',
            static::JUDGE_TWO => 'Second Judge Scores',
            static::JUDGE_THREE => 'Third Judge Scores',
        };
    }
}
