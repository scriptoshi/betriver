<?php

namespace App\Enums;

use App\Enums\Afl\ScoreType as AflScoreType;
use App\Enums\Baseball\ScoreType as BaseballScoreType;
use App\Enums\Basketball\ScoreType as BasketballScoreType;
use App\Enums\Handball\ScoreType as HandballScoreType;
use App\Enums\Hockey\ScoreType as HockeyScoreType;
use App\Enums\Soccer\ScoreType;

enum LeagueSport: string
{
    case FOOTBALL = 'football';
    case BASEBALL = 'baseball';
    case BASKETBALL = 'basketball';
    case VOLLEYBALL = 'volleyball';
    case HANDBALL = 'handball';
    case HOCKEY = 'hockey';
    case AFL = 'afl';
    case NBA = 'nba';
    case NFL = 'nfl';
    case RUGBY = 'rugby';
        // competitions
    case MMA = 'mma';
    case FORMULA1 = 'formula1';
    case POLITICS = 'politics';
    case RACING = 'racing';

    /**
     * Get the method of scores required for a sport
     */
    public function scores()
    {
        return match ($this) {
            static::FOOTBALL => ScoreType::cases(),
            static::AFL => AflScoreType::cases(),
            static::BASEBALL => BaseballScoreType::cases(),
            static::BASKETBALL => BasketballScoreType::cases(),
            static::HANDBALL => HandballScoreType::cases(),
            static::HOCKEY => HockeyScoreType::cases(),
        };
    }
}
