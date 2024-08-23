<?php

namespace App\Enums;

use App\Contracts\BetMarket;
use App\Enums\Soccer\Markets\AsianHandicap;
use App\Enums\Soccer\Markets\BothTeamsToScore;
use App\Enums\Soccer\Markets\CorrectScore;
use App\Enums\Soccer\Markets\DoubleChance;
use App\Enums\Soccer\Markets\ExactGoals;
use App\Enums\Soccer\Markets\GameResult;
use App\Enums\Soccer\Markets\GoalRanges;
use App\Enums\Soccer\Markets\GoalsOverUnder;
use App\Enums\Soccer\Markets\Handicap;
use App\Enums\Soccer\Markets\HighestScoringHalf;
use App\Enums\Soccer\Markets\OddEven;
use App\Enums\Soccer\Markets\ResultAndBothTeamsToScore;
use App\Enums\Soccer\Markets\ScoreBothHalves;
use App\Enums\Soccer\Markets\WhichTeamToScore;
use App\Enums\Soccer\Markets\WinBothHalves;
use App\Enums\Soccer\Markets\WinEitherHalf;
use App\Enums\Soccer\Markets\WinnerAndTotalGoals;
use App\Enums\Soccer\Markets\WinningMargin;
use App\Enums\Soccer\Markets\WinToNil;
// NFL
use App\Enums\Afl\Markets\AFLAsianHandicap;
use App\Enums\Afl\Markets\AFLHalfTimeFullTime;
use App\Enums\Afl\Markets\AFLHighestScoring;
use App\Enums\Afl\Markets\AFLMatchResult;
use App\Enums\Afl\Markets\AFLOddEven;
use App\Enums\Afl\Markets\AFLOverUnder;
use App\Enums\Afl\Markets\AFLPeriodResults;
use App\Enums\Afl\Markets\AFLTeamTotals;
use App\Enums\Afl\Markets\AFLTotalScores;
use App\Enums\Afl\Markets\AFLWinningMargin;
//NFL
use App\Enums\Nfl\Markets\NFLAsianHandicap;
use App\Enums\Nfl\Markets\NFLHalfTimeFullTime;
use App\Enums\Nfl\Markets\NFLHighestScoring;
use App\Enums\Nfl\Markets\NFLMatchResult;
use App\Enums\Nfl\Markets\NFLOddEven;
use App\Enums\Nfl\Markets\NFLOverUnder;
use App\Enums\Nfl\Markets\NFLPeriodResults;
use App\Enums\Nfl\Markets\NFLTeamTotals;
use App\Enums\Nfl\Markets\NFLWinningMargin;
use App\Enums\Baseball\Markets\ExtraInnings;
use App\Enums\Baseball\Markets\MatchResult as BaseballMatchResult;
use App\Enums\Baseball\Markets\Handicap as BaseballHandicap;
use App\Enums\Baseball\Markets\MoneyLine;
use App\Enums\Baseball\Markets\OddEven as BaseballOddEven;
use App\Enums\Baseball\Markets\OverUnder;
use App\Enums\Baseball\Markets\TeamTotals;
use App\Enums\Baseball\Markets\WinningMargin as BaseballWinningMargin;
use App\Enums\Basketball\Markets\AsianHandicap as BasketballAsianHandicap;
use App\Enums\Basketball\Markets\HTFTDouble;
use App\Enums\Basketball\Markets\MatchWinner;
use App\Enums\Basketball\Markets\OddEven as BasketballOddEven;
use App\Enums\Basketball\Markets\OverUnder as BasketballOverUnder;
use App\Enums\Handball\Markets\MatchResult as HandballMatchResult;
use App\Enums\Handball\Markets\Handicap as HandballHandicap;
use App\Enums\Handball\Markets\OverUnder as HandballOverUnder;
use App\Enums\Handball\Markets\DoubleChance as HandballDoubleChance;
use App\Enums\Handball\Markets\OddEven as HandballOddEven;
use App\Enums\Handball\Markets\HalftimeFulltime as HandballHalftimeFulltime;
use App\Enums\Handball\Markets\HighestScoringHalf as HandballHighestScoringHalf;
use App\Enums\Handball\Markets\TeamGoals as HandballTeamGoals;
use App\Enums\Handball\Markets\ResultTotalGoals as HandballResultTotalGoals;
use App\Enums\Handball\Markets\WinToNil as HandballWinToNil;
use App\Enums\Handball\Markets\CorrectScore as HandballCorrectScore;
use App\Enums\Handball\Markets\WinBothHalves as HandballWinBothHalves;
use App\Enums\Handball\Markets\ExactGoals as HandballExactGoals;
use App\Enums\Handball\Markets\WinEitherHalf as HandballWinEitherHalf;
use App\Enums\Handball\Markets\ResultBothTeamsToScore as HandballResultBothTeamsToScore;
use App\Enums\Handball\Markets\WinningMargin as HandballWinningMargin;
//hockey
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
use App\Enums\MMA\Markets\MMAFightDuration;
use App\Enums\MMA\Markets\MMAHandicapOverUnder;
use App\Enums\MMA\Markets\MMAMatchResult;
use App\Enums\MMA\Markets\MMAOverUnder;
use App\Enums\MMA\Markets\MMARoundBetting;
use App\Enums\MMA\Markets\MMAVictoryMethod;
use App\Enums\Races\Markets\Winner;
use App\Enums\Rugby\Markets\RugbyAsianHandicap;
use App\Enums\Rugby\Markets\RugbyDoubleChance;
use App\Enums\Rugby\Markets\RugbyExactGoals;
use App\Enums\Rugby\Markets\RugbyHalfTimeFullTime;
use App\Enums\Rugby\Markets\RugbyHandicapResult;
use App\Enums\Rugby\Markets\RugbyHighestScoringHalf;
use App\Enums\Rugby\Markets\RugbyMatchResult;
use App\Enums\Rugby\Markets\RugbyOddEven;
use App\Enums\Rugby\Markets\RugbyOverUnder;
use App\Enums\Rugby\Markets\RugbyTeamTotals;
use App\Enums\Rugby\Markets\RugbyWinBothHalves;
use App\Enums\Rugby\Markets\RugbyWinEitherHalf;
use App\Enums\Rugby\Markets\RugbyWinningMargin;
use App\Enums\Volleyball\Markets\VolleyballAsianHandicap;
use App\Enums\Volleyball\Markets\VolleyballBothTeamsToScore;
use App\Enums\Volleyball\Markets\VolleyballCorrectScore;
use App\Enums\Volleyball\Markets\VolleyballMatchResult;
use App\Enums\Volleyball\Markets\VolleyballOddEven;
use App\Enums\Volleyball\Markets\VolleyballOverUnder;
use App\Enums\Volleyball\Markets\VolleyballTeamToScore;
use Illuminate\Support\Collection;

enum Market: string
{
    case ASIAN_HANDICAP = AsianHandicap::class;
    case BOTH_TEAMS_TO_SCORE = BothTeamsToScore::class;
    case CORRECT_SCORE = CorrectScore::class;
    case DOUBLE_CHANCE = DoubleChance::class;
    case EXACT_GOALS = ExactGoals::class;
    case GAME_RESULT = GameResult::class;
    case GOAL_RANGES = GoalRanges::class;
    case GOALS_OVER_UNDER = GoalsOverUnder::class;
    case HANDICAP = Handicap::class;
    case HIGHEST_SCORING_HALF = HighestScoringHalf::class;
    case ODD_EVEN = OddEven::class;
    case RESULT_AND_BOTH_TEAMS_TO_SCORE = ResultAndBothTeamsToScore::class;
    case SCORE_BOTH_HALVES = ScoreBothHalves::class;
    case WHICH_TEAM_TO_SCORE = WhichTeamToScore::class;
    case WIN_BOTH_HALVES = WinBothHalves::class;
    case WIN_EITHER_HALF = WinEitherHalf::class;
    case WINNER_AND_TOTAL_GOALS = WinnerAndTotalGoals::class;
    case WINNING_MARGIN = WinningMargin::class;
    case WIN_TO_NIL = WinToNil::class;
        // AFL
    case AFL_ASIAN_HANDICAP = AFLAsianHandicap::class;
    case AFL_HALF_TIME_FULL_TIME = AFLHalfTimeFullTime::class;
    case AFL_HIGHEST_SCORING = AFLHighestScoring::class;
    case AFL_MATCH_RESULT = AFLMatchResult::class;
    case AFL_ODD_EVEN = AFLOddEven::class;
    case AFL_OVER_UNDER = AFLOverUnder::class;
    case AFL_PERIOD_RESULTS = AFLPeriodResults::class;
    case AFL_TEAM_TOTALS = AFLTeamTotals::class;
    case AFL_TOTAL_SCORES = AFLTotalScores::class;
    case AFL_WINNING_MARGIN = AFLWinningMargin::class;
        // NFL
    case NFL_ASIAN_HANDICAP = NFLAsianHandicap::class;
    case NFL_HALF_TIME_FULL_TIME = NFLHalfTimeFullTime::class;
    case NFL_HIGHEST_SCORING = NFLHighestScoring::class;
    case NFL_MATCH_RESULT = NFLMatchResult::class;
    case NFL_ODD_EVEN = NFLOddEven::class;
    case NFL_OVER_UNDER = NFLOverUnder::class;
    case NFL_PERIOD_RESULTS = NFLPeriodResults::class;
    case NFL_TEAM_TOTALS = NFLTeamTotals::class;
    case NFL_WINNING_MARGIN = NFLWinningMargin::class;
        // baseball
    case BASEBALL_HANDICAP = BaseballHandicap::class;
    case BASEBALL_MATCH_RESULT = BaseballMatchResult::class;
    case BASEBALL_TEAM_TOTALS = TeamTotals::class;
    case BASEBALL_OVER_UNDER = OverUnder::class;
    case BASEBALL_ODD_EVEN = BaseballOddEven::class;
    case BASEBALL_MONEY_LINE = MoneyLine::class;
    case BASEBALL_WINNING_MARGIN = BaseballWinningMargin::class;
    case BASEBALL_EXTRA_INNINGS = ExtraInnings::class;
        // basketball
    case BASKETBALL_MATCH_WINNER = MatchWinner::class;
    case BASKETBALL_ASIAN_HANDICAP = BasketballAsianHandicap::class;
    case BASKETBALL_OVER_UNDER = BasketballOverUnder::class;
    case BASKETBALL_ODD_EVEN = BasketballOddEven::class;
    case BASKETBALL_HTFT_DOUBLE = HTFTDouble::class;

        // handball
    case HANDBALL_MATCH_RESULT = HandballMatchResult::class;
    case HANDBALL_HANDICAP = HandballHandicap::class;
    case HANDBALL_OVER_UNDER = HandballOverUnder::class;
    case HANDBALL_DOUBLE_CHANCE = HandballDoubleChance::class;
    case HANDBALL_ODD_EVEN = HandballOddEven::class;
    case HANDBALL_HALFTIME_FULLTIME = HandballHalftimeFulltime::class;
    case HANDBALL_HIGHEST_SCORING_HALF = HandballHighestScoringHalf::class;
    case HANDBALL_TEAM_GOALS = HandballTeamGoals::class;
    case HANDBALL_RESULT_TOTAL_GOALS = HandballResultTotalGoals::class;
    case HANDBALL_WIN_TO_NIL = HandballWinToNil::class;
    case HANDBALL_CORRECT_SCORE = HandballCorrectScore::class;
    case HANDBALL_WIN_BOTH_HALVES = HandballWinBothHalves::class;
    case HANDBALL_EXACT_GOALS = HandballExactGoals::class;
    case HANDBALL_WIN_EITHER_HALF = HandballWinEitherHalf::class;
    case HANDBALL_RESULT_BOTH_TEAMS_TO_SCORE = HandballResultBothTeamsToScore::class;
    case HANDBALL_WINNING_MARGIN = HandballWinningMargin::class;

        //hockey
    case HOCKEY_CORRECT_SCORE = HockeyCorrectScore::class;
    case HOCKEY_DOUBLE_CHANCE = HockeyDoubleChance::class;
    case HOCKEY_EXACT_GOALS = HockeyExactGoals::class;
    case HOCKEY_HANDICAP = HockeyHandicap::class;
    case HOCKEY_MATCH_RESULT = HockeyMatchResult::class;
    case HOCKEY_ODD_EVEN = HockeyOddEven::class;
    case HOCKEY_OVER_UNDER = HockeyOverUnder::class;
    case HOCKEY_RESULT_BOTH_TEAMS_TO_SCORE = HockeyResultBothTeamsToScore::class;
    case HOCKEY_RESULT_TOTAL_GOALS = HockeyResultTotalGoals::class;
    case HOCKEY_TEAM_GOALS = HockeyTeamGoals::class;
    case HOCKEY_TEAM_TO_SCORE = HockeyTeamToScore::class;
    case HOCKEY_WINNING_MARGIN = HockeyWinningMargin::class;
    case HOCKEY_WIN_TO_NIL = HockeyWinToNil::class;
        //mma
    case MMA_MATCH_RESULT = MMAMatchResult::class;
    case MMA_OVER_UNDER = MMAOverUnder::class;
    case MMA_FIGHT_DURATION = MMAFightDuration::class;
    case MMA_ROUND_BETTING = MMARoundBetting::class;
    case MMA_VICTORY_METHOD = MMAVictoryMethod::class;
        // Rugby
    case RUGBY_MATCH_RESULT = RugbyMatchResult::class;
    case RUGBY_ASIAN_HANDICAP = RugbyAsianHandicap::class;
    case RUGBY_HANDICAP_RESULT = RugbyHandicapResult::class;
    case RUGBY_HALF_TIME_FULL_TIME = RugbyHalfTimeFullTime::class;
    case RUGBY_HIGHEST_SCORING_HALF = RugbyHighestScoringHalf::class;
    case RUGBY_OVER_UNDER = RugbyOverUnder::class;
    case RUGBY_TEAM_TOTALS = RugbyTeamTotals::class;
    case RUGBY_ODD_EVEN = RugbyOddEven::class;
    case RUGBY_WIN_BOTH_HALVES = RugbyWinBothHalves::class;
    case RUGBY_WIN_EITHER_HALF = RugbyWinEitherHalf::class;
    case RUGBY_DOUBLE_CHANCE = RugbyDoubleChance::class;
    case RUGBY_EXACT_GOALS = RugbyExactGoals::class;
    case RUGBY_WINNING_MARGIN = RugbyWinningMargin::class;
        // volleyball
    case VOLLEYBALL_MATCH_RESULT = VolleyballMatchResult::class;
    case VOLLEYBALL_OVER_UNDER = VolleyballOverUnder::class;
    case VOLLEYBALL_CORRECT_SCORE = VolleyballCorrectScore::class;
    case VOLLEYBALL_ODD_EVEN = VolleyballOddEven::class;
    case VOLLEYBALL_ASIAN_HANDICAP = VolleyballAsianHandicap::class;
    case VOLLEYBALL_BOTH_TEAMS_TO_SCORE = VolleyballBothTeamsToScore::class;
    case VOLLEYBALL_TEAM_TO_SCORE = VolleyballTeamToScore::class;
        // Races
    case RACING_WINNER = Winner::class;


    public function initialize(): Collection
    {
        return  collect($this->value::cases());
    }

    public function make($from): BetMarket
    {
        return  $this->value::from($from);
    }

    public static function seedGroupOne()
    {
        return [
            static::ASIAN_HANDICAP,
            static::BOTH_TEAMS_TO_SCORE,
            static::CORRECT_SCORE,
            static::DOUBLE_CHANCE,
            static::EXACT_GOALS,
            static::GAME_RESULT,
            static::GOAL_RANGES,
            static::GOALS_OVER_UNDER,
            static::HANDICAP,
            static::HIGHEST_SCORING_HALF,
            static::ODD_EVEN,
            static::RESULT_AND_BOTH_TEAMS_TO_SCORE,
            static::SCORE_BOTH_HALVES,
            static::WHICH_TEAM_TO_SCORE,
            static::WIN_BOTH_HALVES,
            static::WIN_EITHER_HALF,
            static::WINNER_AND_TOTAL_GOALS,
            static::WINNING_MARGIN,
            static::WIN_TO_NIL,
            // AFL
            static::AFL_ASIAN_HANDICAP,
            static::AFL_HALF_TIME_FULL_TIME,
            static::AFL_HIGHEST_SCORING,
            static::AFL_MATCH_RESULT,
            static::AFL_ODD_EVEN,
            static::AFL_OVER_UNDER,
            static::AFL_PERIOD_RESULTS,
            static::AFL_TEAM_TOTALS,
            static::AFL_TOTAL_SCORES,
            static::AFL_WINNING_MARGIN,
            // NFL
            static::NFL_ASIAN_HANDICAP,
            static::NFL_HALF_TIME_FULL_TIME,
            static::NFL_HIGHEST_SCORING,
            static::NFL_MATCH_RESULT,
            static::NFL_ODD_EVEN,
            static::NFL_OVER_UNDER,
            static::NFL_PERIOD_RESULTS,
            static::NFL_TEAM_TOTALS,
            static::NFL_WINNING_MARGIN,
            // baseball
            static::BASEBALL_HANDICAP,
            static::BASEBALL_MATCH_RESULT,
            static::BASEBALL_TEAM_TOTALS,
            static::BASEBALL_OVER_UNDER,
            static::BASEBALL_ODD_EVEN,
            static::BASEBALL_MONEY_LINE,
            static::BASEBALL_WINNING_MARGIN,
            static::BASEBALL_EXTRA_INNINGS,
            // basketball
            static::BASKETBALL_MATCH_WINNER,
            static::BASKETBALL_ASIAN_HANDICAP,
            static::BASKETBALL_OVER_UNDER,
            static::BASKETBALL_ODD_EVEN,
            static::BASKETBALL_HTFT_DOUBLE,
        ];
    }

    public static function seedGroupTwo()
    {
        return [
            static::HANDBALL_MATCH_RESULT,
            static::HANDBALL_HANDICAP,
            static::HANDBALL_OVER_UNDER,
            static::HANDBALL_DOUBLE_CHANCE,
            static::HANDBALL_ODD_EVEN,
            static::HANDBALL_HALFTIME_FULLTIME,
            static::HANDBALL_HIGHEST_SCORING_HALF,
            static::HANDBALL_TEAM_GOALS,
            static::HANDBALL_RESULT_TOTAL_GOALS,
            static::HANDBALL_WIN_TO_NIL,
            static::HANDBALL_CORRECT_SCORE,
            static::HANDBALL_WIN_BOTH_HALVES,
            static::HANDBALL_EXACT_GOALS,
            static::HANDBALL_WIN_EITHER_HALF,
            static::HANDBALL_RESULT_BOTH_TEAMS_TO_SCORE,
            static::HANDBALL_WINNING_MARGIN,
            static::HOCKEY_CORRECT_SCORE,
            static::HOCKEY_DOUBLE_CHANCE,
            static::HOCKEY_EXACT_GOALS,
            static::HOCKEY_HANDICAP,
            static::HOCKEY_MATCH_RESULT,
            static::HOCKEY_ODD_EVEN,
            static::HOCKEY_OVER_UNDER,
            static::HOCKEY_RESULT_BOTH_TEAMS_TO_SCORE,
            static::HOCKEY_RESULT_TOTAL_GOALS,
            static::HOCKEY_TEAM_GOALS,
            static::HOCKEY_TEAM_TO_SCORE,
            static::HOCKEY_WINNING_MARGIN,
            static::HOCKEY_WIN_TO_NIL,
            static::RUGBY_MATCH_RESULT,
            static::RUGBY_ASIAN_HANDICAP,
            static::RUGBY_HANDICAP_RESULT,
            static::RUGBY_HALF_TIME_FULL_TIME,
            static::RUGBY_HIGHEST_SCORING_HALF,
            static::RUGBY_OVER_UNDER,
            static::RUGBY_TEAM_TOTALS,
            static::RUGBY_ODD_EVEN,
            static::RUGBY_WIN_BOTH_HALVES,
            static::RUGBY_WIN_EITHER_HALF,
            static::RUGBY_DOUBLE_CHANCE,
            static::RUGBY_EXACT_GOALS,
            static::RUGBY_WINNING_MARGIN,
            static::VOLLEYBALL_MATCH_RESULT,
            static::VOLLEYBALL_OVER_UNDER,
            static::VOLLEYBALL_CORRECT_SCORE,
            static::VOLLEYBALL_ODD_EVEN,
            static::VOLLEYBALL_ASIAN_HANDICAP,
            static::VOLLEYBALL_BOTH_TEAMS_TO_SCORE,
            static::VOLLEYBALL_TEAM_TO_SCORE,

            //MMA
            static::MMA_MATCH_RESULT,
            static::MMA_OVER_UNDER,
            static::MMA_FIGHT_DURATION,
            static::MMA_ROUND_BETTING,
            static::MMA_VICTORY_METHOD,
            static::RACING_WINNER,
        ];
    }
}
