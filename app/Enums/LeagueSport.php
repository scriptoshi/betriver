<?php

namespace App\Enums;

use App\Api\ApiAfl;
use App\Api\ApiBaseball;
use App\Api\ApiBasketball;
use App\Api\ApiFootball;
use App\Api\ApiHandball;
use App\Api\ApiHokey;
use App\Api\ApiMma;
use App\Api\ApiNfl;
use App\Api\ApiRugby;
use App\Api\ApiVolleyball;
use App\Contracts\GameStatus as ContractsGameStatus;
use App\Enums\Afl\GameStatus as AflGameStatus;
use App\Enums\Afl\ScoreType as AflScoreType;
use App\Enums\Baseball\GameStatus as BaseballGameStatus;
use App\Enums\Baseball\ScoreType as BaseballScoreType;
use App\Enums\Basketball\GameStatus as BasketballGameStatus;
use App\Enums\Basketball\ScoreType as BasketballScoreType;
use App\Enums\Handball\GameStatus as HandballGameStatus;
use App\Enums\Handball\ScoreType as HandballScoreType;
use App\Enums\Hockey\GameStatus as HockeyGameStatus;
use App\Enums\Hockey\ScoreType as HockeyScoreType;
use App\Enums\Mma\GameStatus as MmaGameStatus;
use App\Enums\Mma\ScoreType as MmaScoreType;
use App\Enums\Nfl\GameStatus as NflGameStatus;
use App\Enums\Nfl\ScoreType as NflScoreType;
use App\Enums\Rugby\GameStatus as RugbyGameStatus;
use App\Enums\Rugby\ScoreType as RugbyScoreType;
use App\Enums\Soccer\GameStatus;
use App\Enums\Soccer\ScoreType;
use App\Enums\Volleyball\GameStatus as VolleyballGameStatus;
use App\Enums\Volleyball\ScoreType as VolleyballScoreType;
use App\Http\Resources\League;
use App\Models\Game;

enum LeagueSport: string
{
    case FOOTBALL = 'football';
    case BASEBALL = 'baseball';
    case BASKETBALL = 'basketball';
    case VOLLEYBALL = 'volleyball';
    case HANDBALL = 'handball';
    case HOCKEY = 'hockey';
    case AFL = 'afl';
        //case NBA = 'nba';
    case NFL = 'nfl';
    case RUGBY = 'rugby';
        // competitions
    case MMA = 'mma';
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
            static::NFL => NflScoreType::cases(),
            static::VOLLEYBALL => VolleyballScoreType::cases(),
            static::RUGBY => RugbyScoreType::cases(),
            static::MMA => MmaScoreType::cases(),
        };
    }

    /**
     * This is the win determinant score for a game. 
     * Lost of scores are entered eg goals points, first-half etc
     * 
     */
    public function finalScoreType()
    {
        return match ($this) {
            static::FOOTBALL => ScoreType::TOTAL->value,
            static::AFL => AflScoreType::TOTAL_SCORE->value,
            static::BASEBALL => BaseballScoreType::TOTAL->value,
            static::BASKETBALL => BasketballScoreType::TOTAL->value,
            static::HANDBALL => HandballScoreType::TOTAL->value,
            static::HOCKEY => HockeyScoreType::TOTAL->value,
            static::NFL => NflScoreType::TOTAL->value,
            static::VOLLEYBALL => VolleyballScoreType::TOTAL->value,
            static::RUGBY => RugbyScoreType::TOTAL->value,
            static::MMA => null,
            static::RACING => null,
        };
    }

    public function gameStatus($status): ContractsGameStatus
    {
        $state = match ($this) {
            static::FOOTBALL => GameStatus::class,
            static::BASEBALL => BaseballGameStatus::class,
            static::BASKETBALL => BasketballGameStatus::class,
            static::VOLLEYBALL => VolleyballGameStatus::class,
            static::HANDBALL => HandballGameStatus::class,
            static::HOCKEY => HockeyGameStatus::class,
            static::AFL => AflGameStatus::class,
            //static::NBA => Game,
            static::NFL => NflGameStatus::class,
            static::RUGBY => RugbyGameStatus::class,
            static::MMA => MmaGameStatus::class,
            default => GameStatus::class,
        };
        return $state::from($status);
    }



    public function api()
    {
        return match ($this) {
            static::FOOTBALL => ApiFootball::class,
            static::BASEBALL => ApiBaseball::class,
            static::BASKETBALL => ApiBasketball::class,
            static::VOLLEYBALL => ApiVolleyball::class,
            static::HANDBALL => ApiHandball::class,
            static::HOCKEY => ApiHokey::class,
            static::AFL => ApiAfl::class,
            //static::NBA => Game,
            static::NFL => ApiNfl::class,
            static::RUGBY => ApiRugby::class,
            static::MMA => ApiMma::class,
            default => GameStatus::class,
        };
    }
}
