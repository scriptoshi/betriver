<?php

namespace App\Enums;

use App\Enums\Soccer\Markets\BothTeamsToScore;
use App\Enums\Soccer\Markets\WinBothHalves;
use App\Enums\Soccer\Markets\WinEitherHalfs;

enum MarketType: string
{

    case WIN_EITHER_HALFS = 'WinEitherHalfs';
    case GAME_RESULT = 'GameResult';
    case BOTH_TEAMS_TO_SCORE = 'BothTeamsToScore';
    case CLEAN_SHEET = 'CleanSheet';
    case ASIAN_HANDI_CAP = 'AsianHandiCap';
    case EVEN_ODD_RESULT = 'EvenOddResult';
    case EXACT_GOALS = 'ExactGoals';
    case EXACT_SCORE = 'ExactScore';
    case GOALS_OVER_UNDER = 'GoalsOverUnder';
    case GOALS_RANGE = 'GoalsRange';
    case HALF_TIME_FULL_TIME = 'HalfTimeFullTime';
    case HIGHEST_SCORING_HALF = 'HighestScoringHalf';
    case SCORE_BOTH_HALFS = 'ScoreBothHalfs';
    case SCORES_FIRST_HALF = 'ScoresFirstHalf';
    case TEAM_SCORES_BOTH_HALFS = 'TeamScoresBothHalfs';
    case WIN_BOTH_HALFS = 'WinBothHalfs';
    case COMPOUND_BET = 'CompoundBet';
    case GOLD = 'Gold';
    case SILVER = 'Silver';
    case BRONZE = 'Bronze';

    public function markets(): array
    {
        return match ($this) {
            static::WIN_EITHER_HALFS => WinEitherHalfs::cases(),
            static::WIN_BOTH_HALFS => WinBothHalves::cases(),
            static::BOTH_TEAMS_TO_SCORE => BothTeamsToScore::cases(),
        };
    }
}
