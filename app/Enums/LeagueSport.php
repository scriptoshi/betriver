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
use App\Enums\Afl\Outcomes\AFLAsianHandicapOutcome;
use App\Enums\Afl\Outcomes\AFLOverUnderOutcome;
use App\Enums\Afl\ScoreType as AflScoreType;
use App\Enums\Baseball\GameStatus as BaseballGameStatus;
use App\Enums\Baseball\Outcomes\HandicapOutcome as BaseballHandicapOutcome;
use App\Enums\Baseball\Outcomes\OverUnderOutcome as BaseballOverUnderOutcome;
use App\Enums\Baseball\ScoreType as BaseballScoreType;
use App\Enums\Basketball\GameStatus as BasketballGameStatus;
use App\Enums\Basketball\Outcomes\AsianHandicapOutcome as BasketballAsianHandicapOutcome;
use App\Enums\Basketball\Outcomes\OverUnderOutcome as BasketballOverUnderOutcome;
use App\Enums\Basketball\ScoreType as BasketballScoreType;
use App\Enums\Handball\GameStatus as HandballGameStatus;
use App\Enums\Handball\Outcomes\HandicapOutcome as HandballHandicapOutcome;
use App\Enums\Handball\Outcomes\OverUnderOutcome as HandballOverUnderOutcome;
use App\Enums\Handball\ScoreType as HandballScoreType;
use App\Enums\Hockey\GameStatus as HockeyGameStatus;
use App\Enums\Hockey\Outcomes\HandicapOutcome as HockeyHandicapOutcome;
use App\Enums\Hockey\Outcomes\OverUnderOutcome as HockeyOverUnderOutcome;
use App\Enums\Hockey\ScoreType as HockeyScoreType;
use App\Enums\Mma\GameStatus as MmaGameStatus;
use App\Enums\MMA\Outcomes\MMAOverUnderOutcome;
use App\Enums\Mma\ScoreType as MmaScoreType;
use App\Enums\Nfl\GameStatus as NflGameStatus;
use App\Enums\Nfl\ScoreType as NflScoreType;
use App\Enums\Rugby\GameStatus as RugbyGameStatus;
use App\Enums\Rugby\Outcomes\RugbyAsianHandicapOutcome;
use App\Enums\Rugby\Outcomes\RugbyOverUnderOutcome;
use App\Enums\Rugby\ScoreType as RugbyScoreType;
use App\Enums\Soccer\GameStatus;
use App\Enums\Soccer\Markets\GoalsOverUnder;
use App\Enums\Soccer\Outcomes\AsianHandicapOutcome as SoccerAsianHandicapOutcome;
use App\Enums\Soccer\Outcomes\GoalsOverUnderOutcome as SoccerGoalsOverUnderOutcome;
use App\Enums\Soccer\ScoreType;
use App\Enums\Volleyball\GameStatus as VolleyballGameStatus;
use App\Enums\Volleyball\Outcomes\VolleyballAsianHandicapOutcome;
use App\Enums\Volleyball\Outcomes\VolleyballOverUnderOutcome;
use App\Enums\Volleyball\ScoreType as VolleyballScoreType;

enum LeagueSport: string
{
    case FOOTBALL = 'football';
    case BASEBALL = 'baseball';
    case BASKETBALL = 'basketball';
    case VOLLEYBALL = 'volleyball';
    case HANDBALL = 'handball';
    case HOCKEY = 'hockey';
    case AFL = 'afl';
    case NFL = 'nfl';
    case RUGBY = 'rugby';
    case MMA = 'mma';
    case RACING = 'racing';


    public function overunders(): ?array
    {
        return match ($this) {
            static::FOOTBALL => SoccerGoalsOverUnderOutcome::getOverUnders(),
            static::BASEBALL => BaseballOverUnderOutcome::getOverUnders(),
            static::BASKETBALL => BasketballOverUnderOutcome::getOverUnders(),
            static::VOLLEYBALL => VolleyballOverUnderOutcome::getOverUnders(),
            static::HANDBALL => HandballOverUnderOutcome::getOverUnders(),
            static::HOCKEY => HockeyOverUnderOutcome::getOverUnders(),
            static::NFL, static::AFL => AFLOverUnderOutcome::getOverUnders(),
            //static::NBA => Game,

            static::RUGBY => RugbyOverUnderOutcome::getOverUnders(),
            static::MMA => MMAOverUnderOutcome::getOverUnders(),
            default => [],
        };
    }

    /**
     * Get the human-readable name for the league sport.
     *
     * @return string
     */
    public function name(): string
    {
        return match ($this) {
            self::FOOTBALL => 'Football',
            self::BASEBALL => 'Baseball',
            self::BASKETBALL => 'Basketball',
            self::VOLLEYBALL => 'Volleyball',
            self::HANDBALL => 'Handball',
            self::HOCKEY => 'Hockey',
            self::AFL => 'AFL',
            self::NFL => 'NFL',
            self::RUGBY => 'Rugby',
            self::MMA => 'MMA',
            self::RACING => 'Racing',
        };
    }

    /**
     * Get an array of all league sport names.
     *
     * @return array<string, array>
     */
    public static function getNames(): array
    {
        return array_reduce(self::cases(), function ($carry, $case) {
            $carry[$case->value] = [
                'name' => $case->name(),
                'value' => $case->value
            ];
            return $carry;
            return $carry;
        }, []);
    }

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

    public function gameStatus($status): ?ContractsGameStatus
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
        return $state::tryFrom($status);
    }

    public function handicaps(): ?array
    {
        return match ($this) {
            static::FOOTBALL => SoccerAsianHandicapOutcome::getHandicaps(),
            static::BASEBALL => BaseballHandicapOutcome::getHandicaps(),
            static::BASKETBALL => BasketballAsianHandicapOutcome::getHandicaps(),
            static::VOLLEYBALL => VolleyballAsianHandicapOutcome::getHandicaps(),
            static::HANDBALL => HandballHandicapOutcome::getHandicaps(),
            static::HOCKEY => HockeyHandicapOutcome::getHandicaps(),
            static::NFL, static::AFL => AFLAsianHandicapOutcome::getHandicaps(),
            //static::NBA => Game,
            static::RUGBY => RugbyAsianHandicapOutcome::getHandicaps(),
            static::MMA => [],
            default => [],
        };
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
