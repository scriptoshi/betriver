<?php

namespace App\Enums;

use App\Contracts\BetMarket;

use  App\Enums\Afl\Markets\AFLHalfTimeFullTime;
use  App\Enums\Afl\Markets\AFLAsianHandicap;
use  App\Enums\Afl\Markets\AFLHighestScoring;
use  App\Enums\Afl\Markets\AFLMatchResult;
use  App\Enums\Afl\Markets\AFLOddEven;
use  App\Enums\Afl\Markets\AFLOverUnder;
use  App\Enums\Afl\Markets\AFLPeriodResults;
use  App\Enums\Afl\Markets\AFLTeamTotals;
use  App\Enums\Afl\Markets\AFLTotalScores;
use  App\Enums\Afl\Markets\AFLWinningMargin;
use  App\Enums\Baseball\Markets\ExtraInnings;
use App\Enums\Baseball\Markets\Handicap as BaseballHandicap;
use App\Enums\Baseball\Markets\MatchResult as BaseballMatchResult;
use App\Enums\Baseball\Markets\MoneyLine;
use App\Enums\Baseball\Markets\OddEven as BaseballOddEven;
use App\Enums\Baseball\Markets\OverUnder as BaseballOverUnder;
use App\Enums\Baseball\Markets\TeamTotals;
use App\Enums\Baseball\Markets\WinningMargin as BaseballWinningMargin;

use App\Enums\Handball\Markets\CorrectScore as HandballCorrectScore;
use App\Enums\Handball\Markets\DoubleChance as HandballDoubleChance;
use App\Enums\Handball\Markets\ExactGoals as HandballExactGoals;
use App\Enums\Handball\Markets\HalftimeFulltime;
use App\Enums\Handball\Markets\Handicap as HandballHandicap;
use App\Enums\Handball\Markets\HighestScoringHalf as HandballHighestScoringHalf;
use App\Enums\Handball\Markets\MatchResult as HandballMatchResult;
use App\Enums\Handball\Markets\OddEven as HandballOddEven;
use App\Enums\Handball\Markets\OverUnder as HandballOverUnder;
use App\Enums\Handball\Markets\ResultBothTeamsToScore as HandballResultBothTeamsToScore;
use App\Enums\Handball\Markets\ResultTotalGoals as HandballResultTotalGoals;
use App\Enums\Handball\Markets\TeamGoals as HandballTeamGoals;
use App\Enums\Handball\Markets\TeamToScore as HandballTeamToScore;
use App\Enums\Handball\Markets\WinBothHalves as HandballWinBothHalves;
use App\Enums\Handball\Markets\WinEitherHalf as HandballWinEitherHalf;
use App\Enums\Handball\Markets\WinningMargin as HandballWinningMargin;
use App\Enums\Handball\Markets\WinToNil as HandballWinToNil;

use App\Enums\Hockey\Markets\CorrectScore as HockeyCorrectScore;
use App\Enums\Hockey\Markets\DoubleChance as HockeyDoubleChance;
use App\Enums\Hockey\Markets\ExactGoals as HockeyExactGoals;
use App\Enums\Hockey\Markets\Handicap as HockeyHandicap;
use App\Enums\Hockey\Markets\MatchResult as HockeyMatchResult;
use App\Enums\Hockey\Markets\OddEven as HockeyOddEven;
use App\Enums\Hockey\Markets\OverUnder as HockeyOverUnder;
use App\Enums\Hockey\Markets\ResultBothTeamsToScore as HockeyResultBothTeamsToScore;
use App\Enums\Hockey\Markets\ResultTotalGoals as HockeyResultTotalGoals;
use App\Enums\Hockey\Markets\TeamGoals as HockeyTeamGoals;
use App\Enums\Hockey\Markets\TeamToScore as HockeyTeamToScore;
use App\Enums\Hockey\Markets\WinningMargin as HockeyWinningMargin;
use App\Enums\Hockey\Markets\WinToNil as HockeyWinToNil;

use  App\Enums\Mma\Markets\MMAFightDuration;
use  App\Enums\Mma\Markets\MMAMatchResult;
use  App\Enums\Mma\Markets\MMAOverUnder;
use  App\Enums\Mma\Markets\MMARoundBetting;
use  App\Enums\Mma\Markets\MMAVictoryMethod;
use  App\Enums\Nfl\Markets\NFLAsianHandicap;
use  App\Enums\Nfl\Markets\NFLHalfTimeFullTime;
use  App\Enums\Nfl\Markets\NFLHighestScoring;
use  App\Enums\Nfl\Markets\NFLMatchResult;
use  App\Enums\Nfl\Markets\NFLOddEven;
use  App\Enums\Nfl\Markets\NFLOverUnder;
use  App\Enums\Nfl\Markets\NFLPeriodResults;
use  App\Enums\Nfl\Markets\NFLTeamTotals;
use  App\Enums\Nfl\Markets\NFLWinningMargin;
use  App\Enums\Rugby\Markets\RugbyAsianHandicap;
use  App\Enums\Rugby\Markets\RugbyDoubleChance;
use  App\Enums\Rugby\Markets\RugbyExactGoals;
use  App\Enums\Rugby\Markets\RugbyHalfTimeFullTime;
use  App\Enums\Rugby\Markets\RugbyHandicapResult;
use  App\Enums\Rugby\Markets\RugbyHighestScoringHalf;
use  App\Enums\Rugby\Markets\RugbyMatchResult;
use  App\Enums\Rugby\Markets\RugbyOddEven;
use  App\Enums\Rugby\Markets\RugbyOverUnder;
use  App\Enums\Rugby\Markets\RugbyTeamTotals;
use  App\Enums\Rugby\Markets\RugbyWinBothHalves;
use  App\Enums\Rugby\Markets\RugbyWinEitherHalf;
use  App\Enums\Rugby\Markets\RugbyWinningMargin;
use  App\Enums\Soccer\Markets\AsianHandicap;
use  App\Enums\Soccer\Markets\BothTeamsToScore;

use App\Enums\Soccer\Markets\CorrectScore as SoccerCorrectScore;
use App\Enums\Soccer\Markets\DoubleChance as SoccerDoubleChance;
use App\Enums\Soccer\Markets\ExactGoals as SoccerExactGoals;
use App\Enums\Soccer\Markets\Handicap as SoccerHandicap;
use App\Enums\Soccer\Markets\HighestScoringHalf as SoccerHighestScoringHalf;
use App\Enums\Soccer\Markets\OddEven as SoccerOddEven;
use App\Enums\Soccer\Markets\WinBothHalves as SoccerWinBothHalves;
use App\Enums\Soccer\Markets\WinEitherHalf as SoccerWinEitherHalf;
use App\Enums\Soccer\Markets\WinningMargin as SoccerWinningMargin;
use App\Enums\Soccer\Markets\WinToNil as SoccerWinToNil;
use  App\Enums\Soccer\Markets\ResultAndBothTeamsToScore;
use  App\Enums\Soccer\Markets\ScoreBothHalves;
use  App\Enums\Soccer\Markets\WhichTeamToScore;
use  App\Enums\Soccer\Markets\WinnerAndTotalGoals;
use  App\Enums\Soccer\Markets\GameResult;
use  App\Enums\Soccer\Markets\GoalRanges;
use  App\Enums\Soccer\Markets\GoalsOverUnder;
use  App\Enums\Volleyball\Markets\VolleyballAsianHandicap;
use  App\Enums\Volleyball\Markets\VolleyballBothTeamsToScore;
use  App\Enums\Volleyball\Markets\VolleyballCorrectScore;
use  App\Enums\Volleyball\Markets\VolleyballMatchResult;
use  App\Enums\Volleyball\Markets\VolleyballOddEven;
use  App\Enums\Volleyball\Markets\VolleyballOverUnder;
use  App\Enums\Volleyball\Markets\VolleyballTeamToScore;


use App\Enums\Basketball\Markets\AsianHandicap as BasketballAsianHandicap;
use App\Enums\Basketball\Markets\HTFTDouble;
use App\Enums\Basketball\Markets\MatchWinner;
use App\Enums\Basketball\Markets\OddEven;
use App\Enums\Basketball\Markets\OverUnder;

enum MarketCategory: string
{
        //
    case WINNER = 'winner';
    case TEAMS = 'teams';
    case TOTALS = 'totals';
    case HANDICAP = 'handicap';
    case HALF = 'half';
    case GOALS = 'goals';

    public static function getCategory(string $market): MarketCategory
    {
        return match ($market) {
            // AFL
            AFLHalfTimeFullTime::class, AFLMatchResult::class, AFLPeriodResults::class => static::WINNER,
            AFLAsianHandicap::class => static::HANDICAP,
            AFLHighestScoring::class => static::TOTALS,
            AFLOddEven::class => static::TOTALS,
            AFLOverUnder::class => static::TOTALS,
            AFLTeamTotals::class => static::TEAMS,
            AFLTotalScores::class => static::TOTALS,
            AFLWinningMargin::class => static::WINNER,

            // Baseball
            BaseballMatchResult::class, MoneyLine::class => static::WINNER,
            BaseballHandicap::class => static::HANDICAP,
            BaseballOddEven::class => static::TOTALS,
            BaseballOverUnder::class => static::TOTALS,
            TeamTotals::class => static::TEAMS,
            BaseballWinningMargin::class => static::WINNER,
            ExtraInnings::class => static::TOTALS,

            // Handball
            HandballMatchResult::class, HandballDoubleChance::class, HalftimeFulltime::class => static::WINNER,
            HandballCorrectScore::class => static::TOTALS,
            HandballExactGoals::class => static::GOALS,
            HandballHandicap::class => static::HANDICAP,
            HandballHighestScoringHalf::class => static::HALF,
            HandballOddEven::class => static::TOTALS,
            HandballOverUnder::class => static::TOTALS,
            HandballResultBothTeamsToScore::class => static::TEAMS,
            HandballResultTotalGoals::class => static::TOTALS,
            HandballTeamGoals::class => static::TEAMS,
            HandballTeamToScore::class => static::TEAMS,
            HandballWinBothHalves::class, HandballWinEitherHalf::class => static::HALF,
            HandballWinningMargin::class => static::WINNER,
            HandballWinToNil::class => static::TEAMS,

            // Hockey
            HockeyMatchResult::class, HockeyDoubleChance::class => static::WINNER,
            HockeyCorrectScore::class => static::TOTALS,
            HockeyExactGoals::class => static::GOALS,
            HockeyHandicap::class => static::HANDICAP,
            HockeyOddEven::class => static::TOTALS,
            HockeyOverUnder::class => static::TOTALS,
            HockeyResultBothTeamsToScore::class => static::TEAMS,
            HockeyResultTotalGoals::class => static::TOTALS,
            HockeyTeamGoals::class => static::TEAMS,
            HockeyTeamToScore::class => static::TEAMS,
            HockeyWinningMargin::class => static::WINNER,
            HockeyWinToNil::class => static::TEAMS,

            // MMA
            MMAMatchResult::class => static::WINNER,
            MMAFightDuration::class => static::TOTALS,
            MMAOverUnder::class => static::TOTALS,
            MMARoundBetting::class => static::TOTALS,
            MMAVictoryMethod::class => static::WINNER,

            // NFL
            NFLMatchResult::class, NFLHalfTimeFullTime::class => static::WINNER,
            NFLAsianHandicap::class => static::HANDICAP,
            NFLHighestScoring::class => static::TOTALS,
            NFLOddEven::class => static::TOTALS,
            NFLOverUnder::class => static::TOTALS,
            NFLPeriodResults::class => static::WINNER,
            NFLTeamTotals::class => static::TEAMS,
            NFLWinningMargin::class => static::WINNER,

            // Rugby
            RugbyMatchResult::class, RugbyDoubleChance::class, RugbyHalfTimeFullTime::class => static::WINNER,
            RugbyAsianHandicap::class, RugbyHandicapResult::class => static::HANDICAP,
            RugbyExactGoals::class => static::GOALS,
            RugbyHighestScoringHalf::class => static::HALF,
            RugbyOddEven::class => static::TOTALS,
            RugbyOverUnder::class => static::TOTALS,
            RugbyTeamTotals::class => static::TEAMS,
            RugbyWinBothHalves::class, RugbyWinEitherHalf::class => static::HALF,
            RugbyWinningMargin::class => static::WINNER,

            // Soccer
            SoccerCorrectScore::class => static::TOTALS,
            SoccerDoubleChance::class => static::WINNER,
            SoccerExactGoals::class => static::GOALS,
            SoccerHandicap::class, AsianHandicap::class => static::HANDICAP,
            SoccerHighestScoringHalf::class => static::HALF,
            SoccerOddEven::class => static::TOTALS,
            SoccerWinBothHalves::class, SoccerWinEitherHalf::class => static::HALF,
            SoccerWinningMargin::class => static::WINNER,
            SoccerWinToNil::class => static::TEAMS,
            BothTeamsToScore::class => static::TEAMS,
            ResultAndBothTeamsToScore::class => static::TEAMS,
            ScoreBothHalves::class => static::HALF,
            WhichTeamToScore::class => static::TEAMS,
            WinnerAndTotalGoals::class => static::TOTALS,
            GameResult::class => static::WINNER,
            GoalRanges::class => static::GOALS,
            GoalsOverUnder::class => static::TOTALS,

            // Volleyball
            VolleyballMatchResult::class => static::WINNER,
            VolleyballAsianHandicap::class => static::HANDICAP,
            VolleyballBothTeamsToScore::class => static::TEAMS,
            VolleyballCorrectScore::class => static::TOTALS,
            VolleyballOddEven::class => static::TOTALS,
            VolleyballOverUnder::class => static::TOTALS,
            VolleyballTeamToScore::class => static::TEAMS,
            // Basketball (new additions)
            BasketballAsianHandicap::class => self::HANDICAP,
            HTFTDouble::class => self::WINNER,
            MatchWinner::class => self::WINNER,
            OddEven::class => self::TOTALS,
            OverUnder::class => self::TOTALS,

            default => throw new \InvalidArgumentException('Unknown market type: ' . $market),
        };
    }

    
}
