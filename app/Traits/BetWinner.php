<?php

namespace App\Traits;

use App\Enums\BetMode;
use App\Enums\BothHalfsWinner;
use App\Enums\ExactScores;
use App\Enums\GoalCount;
use App\Enums\Goals;
use App\Enums\HighestScores;
use App\Enums\MixedOutcomes;
use App\Enums\Range;
use App\Enums\Result as EnumsResult;
use App\Enums\ResultTime;

trait BetWinner
{
    /**
     * Determine if this bet won
     */
    public function won()
    {
        $bet = $this->bet;
        if ($bet->mode == BetMode::None) return false;
        if (
            $bet->mode == BetMode::Gold ||
            $bet->mode == BetMode::Silver ||
            $bet->mode == BetMode::Bronze
        ) return $this->gold();
        if ($bet->mode == BetMode::GameResult)
            return $this->gameResult();
        if ($bet->mode == BetMode::BothTeamsToScore)
            return $this->bothTeamsToScore();
        if ($bet->mode == BetMode::CleanSheet)
            return $this->cleanSheet();
        if ($bet->mode == BetMode::AsianHandiCap)
            return $this->asianHandicap();
        if ($bet->mode == BetMode::EvenOddResult)
            return $this->evenOddResult();
        if ($bet->mode == BetMode::ExactGoals)
            return $this->exactGoals();
        if ($bet->mode == BetMode::ExactScore)
            return $this->exactScore();
        if ($bet->mode == BetMode::GoalsOverUnder)
            return $this->goalsOverUnder();
        if ($bet->mode == BetMode::GoalsRange)
            return $this->goalsRange();
        if ($bet->mode == BetMode::HalfTimeFullTime)
            return $this->halfTimeFullTime();
        if ($bet->mode == BetMode::HighestScoringHalf)
            return $this->highestScoringHalf();
        if ($bet->mode == BetMode::ScoreBothHalfs)
            return $this->scoresBothHalfs();
        if ($bet->mode == BetMode::ScoresFirstHalf)
            return $this->scoresFirstHalf();
        if ($bet->mode == BetMode::WinBothHalfs)
            return $this->winBothHalfs();
        if ($bet->mode == BetMode::WinEitherHalfs)
            return $this->winEitherHalfs();
        return false;
    }
    /**
     * who takes gold silver bronze
     */

    function gold()
    {
        $game = $this->game;
        $bet = $this->bet;
        if ($bet->mode == BetMode::Gold)
            return $bet->result == $game->gold;
        if ($bet->mode == BetMode::Silver)
            return $bet->result == $game->silver;
        if ($bet->mode == BetMode::Bronze)
            return $bet->result == $game->bronze;
        return false;
    }



    /**
     * check if a Bet on winEitherHalfs wins
     */
    public function winEitherHalfs(): bool
    {
        $game = $this->game;
        $bet = $this->bet;
        $homeWinsEitherHalf = $game->halftimeResult == EnumsResult::HOME
            || $game->result == EnumsResult::HOME;
        $awayWinsEitherHalf = $game->halftimeResult == EnumsResult::AWAY
            || $game->result == EnumsResult::AWAY;

        if ($bet->result == BothHalfsWinner::away->value) {
            return $awayWinsEitherHalf && !$homeWinsEitherHalf;
        }
        if ($bet->result == BothHalfsWinner::home->value) {
            return $homeWinsEitherHalf && !$awayWinsEitherHalf;
        }
        if ($bet->result == BothHalfsWinner::both->value) {
            return  $homeWinsEitherHalf && $awayWinsEitherHalf;
        }
        if ($bet->result == BothHalfsWinner::none->value) {
            return !$homeWinsEitherHalf && !$awayWinsEitherHalf;
        }
        return false;
    }



    /**
     * Determine if this bet won bothhalfswinner
     */
    public function winBothHalfs(): bool
    {
        $game = $this->game;
        $bet = $this->bet;
        $awayWinsBothHalfs = $game->halftimeResult == EnumsResult::AWAY
            && $game->secondHalfResult == EnumsResult::AWAY;
        $homeWinsBothHalfs = $game->halftimeResult == EnumsResult::HOME
            && $game->secondHalfResult == EnumsResult::HOME;

        if ($bet->result == BothHalfsWinner::away->value) {
            return $awayWinsBothHalfs;
        }
        if ($bet->result == BothHalfsWinner::home->value) {
            return $homeWinsBothHalfs;
        }
        // none wins
        return !$homeWinsBothHalfs && !$awayWinsBothHalfs;
    }
    /**
     * check if a bet on who will score in first half wins
     */
    public function scoresFirstHalf()
    {
        $game = $this->game;
        $bet = $this->bet;
        if ($bet->result == BothHalfsWinner::away->value) {
            return $game->awayHalftimeScore > 0 && $game->homeHalftimeScore == 0;
        }
        if ($bet->result == BothHalfsWinner::home->value) {
            return $game->homeHalftimeScore > 0 && $game->awayHalftimeScore == 0;
        }
        if ($bet->result == BothHalfsWinner::both->value) {
            return $game->homeHalftimeScore > 0 && $game->awayHalftimeScore > 0;
        }
        if ($bet->result == BothHalfsWinner::none->value) {
            return $game->homeHalftimeScore == 0 && $game->awayHalftimeScore == 0;
        }
    }

    /**
     * check if a bet on who will score in both halfts wins
     */
    public function scoresBothHalfs()
    {
        $game = $this->game;
        $bet = $this->bet;
        $awayHalftimeScore = $game->awayHalftimeScore;
        $homeHalftimeScore = $game->homeHalftimeScore;
        $awaySecondHalfScore = $game->awayScore - $awayHalftimeScore;
        $homeSecondHalfScore = $game->homeScore - $homeHalftimeScore;
        $homeScoredBothHalfts = $homeHalftimeScore > 0 && $homeSecondHalfScore > 0;
        $awayScoredBothHalfts = $awayHalftimeScore > 0 && $awaySecondHalfScore > 0;
        if ($bet->result == BothHalfsWinner::away->value) {
            return $awayScoredBothHalfts && !$homeScoredBothHalfts;
        }
        if ($bet->result == BothHalfsWinner::home->value) {
            return $homeScoredBothHalfts && !$awayScoredBothHalfts;
        }
        if ($bet->result == BothHalfsWinner::both->value) {
            return $homeScoredBothHalfts && $awayScoredBothHalfts;
        }
        if ($bet->result == BothHalfsWinner::none->value) {
            return !$homeScoredBothHalfts && !$awayScoredBothHalfts;
        }
    }

    /**
     * check if a Bet on highest scoring half wins;
     */
    public   function highestScoringHalf(): bool
    {
        $game = $this->game;
        $bet = $this->bet;
        $totalScores =  $game->getScores(ResultTime::fullTime, $bet->team);
        $halfScores =  $game->getScores(ResultTime::firstHalf, $bet->team);
        $secondHalfScores = $totalScores - $halfScores;
        if ($bet->result == HighestScores::firstHalf->value) {
            return $halfScores > $secondHalfScores;
        }
        if ($bet->result == HighestScores::equal->value) {
            return $halfScores == $secondHalfScores;
        }
        if ($bet->result == HighestScores::secondHalf->value) {
            return $halfScores < $secondHalfScores;
        }
        return false;
    }

    /**
     * check if a Bet on halfTime FullTime wins;
     */
    public   function halfTimeFullTime(): bool
    {
        $game = $this->game;
        $bet = $this->bet;
        if ($bet->result == MixedOutcomes::AwayAway->value) {
            return $game->halftimeResult == EnumsResult::AWAY
                && $game->result == EnumsResult::AWAY;
        }
        if ($bet->result == MixedOutcomes::AwayHome->value) {
            return $game->halftimeResult == EnumsResult::AWAY
                && $game->result == EnumsResult::HOME;
        }
        if ($bet->result == MixedOutcomes::AwayDraw->value) {
            return $game->halftimeResult == EnumsResult::AWAY
                && $game->result == EnumsResult::DRAW;
        }
        if ($bet->result == MixedOutcomes::HomeAway->value) {
            return $game->halftimeResult == EnumsResult::HOME
                && $game->result == EnumsResult::AWAY;
        }
        if ($bet->result == MixedOutcomes::HomeHome->value) {
            return $game->halftimeResult == EnumsResult::HOME
                && $game->result == EnumsResult::HOME;
        }
        if ($bet->result == MixedOutcomes::HomeDraw->value) {
            return $game->halftimeResult == EnumsResult::HOME
                && $game->result == EnumsResult::DRAW;
        }
        if ($bet->result == MixedOutcomes::DrawAway->value) {
            return $game->halftimeResult == EnumsResult::DRAW
                && $game->result == EnumsResult::AWAY;
        }
        if ($bet->result == MixedOutcomes::DrawHome->value) {
            return $game->halftimeResult == EnumsResult::DRAW
                && $game->result == EnumsResult::HOME;
        }
        if ($bet->result == MixedOutcomes::DrawDraw->value) {
            return $game->halftimeResult == EnumsResult::DRAW
                && $game->result == EnumsResult::DRAW;
        }
        return false;
    }

    /**
     * check if a Bet on Goal range wins;
     */
    public   function goalsRange(): bool
    {
        $game = $this->game;
        $bet = $this->bet;
        $homeGoals = $game->getScores($bet->gameTime, GoalCount::home);
        $awayGoals = $game->getScores($bet->gameTime, GoalCount::away);
        $goals = abs($homeGoals - $awayGoals);
        if ($bet->result == Range::ZeroToOne->value) return $goals < 2;
        if ($bet->result == Range::TwoToThree->value)
            return $goals > 1 && $goals < 4;
        if ($bet->result == Range::FourToSix->value) return $goals > 3 && $goals < 7;
        return $goals > 6;
    }

    /**
     * check if a Bet on Goals over / Under wins;
     */
    public   function goalsOverUnder(): bool
    {
        $game = $this->game;
        $bet = $this->bet;
        $goals = $game->getScores($bet->gameTime, $bet->team);
        if ($bet->boolOutcome) return $goals > $bet->result;
        return $goals < ($bet->result + 1);
    }

    /**
     * check if a Bet on game result: home away draw
     */
    public function gameResult(): bool
    {
        $game = $this->game;
        $bet = $this->bet;
        $result = $game->getResult($bet->gameTime);
        return $bet->result == $result->value;
    }

    /**
     * check if a Bet on exact scores Wins
     */
    public function exactScore(): bool
    {
        $game = $this->game;
        $bet = $this->bet;
        $awayScore = $game->getScores($bet->gameTime, GoalCount::away);
        $homeScore = $game->getScores($bet->gameTime, GoalCount::home);
        if ($bet->result == ExactScores::ZeroZero->value)
            return $awayScore == 0 && $homeScore == 0;
        if ($bet->result == ExactScores::ZeroOne->value)
            return $awayScore == 0 && $homeScore == 1;
        if ($bet->result == ExactScores::ZeroTwo->value)
            return $awayScore == 0 && $homeScore == 2;
        if ($bet->result == ExactScores::ZeroThree->value)
            return $awayScore == 0 && $homeScore == 3;
        if ($bet->result == ExactScores::OneZero->value)
            return $awayScore == 1 && $homeScore == 0;
        if ($bet->result == ExactScores::OneOne->value)
            return $awayScore == 1 && $homeScore == 1;
        if ($bet->result == ExactScores::OneTwo->value)
            return $awayScore == 1 && $homeScore == 2;
        if ($bet->result == ExactScores::OneThree->value)
            return $awayScore == 1 && $homeScore == 3;
        if ($bet->result == ExactScores::TwoZero->value)
            return $awayScore == 2 && $homeScore == 0;
        if ($bet->result == ExactScores::TwoOne->value)
            return $awayScore == 2 && $homeScore == 1;
        if ($bet->result == ExactScores::TwoTwo->value)
            return $awayScore == 2 && $homeScore == 2;
        if ($bet->result == ExactScores::TwoThree->value)
            return $awayScore == 2 && $homeScore == 3;
        if ($bet->result == ExactScores::ThreeZero->value)
            return $awayScore == 3 && $homeScore == 0;
        if ($bet->result == ExactScores::ThreeOne->value)
            return $awayScore == 3 && $homeScore == 1;
        if ($bet->result == ExactScores::ThreeTwo->value)
            return $awayScore == 3 && $homeScore == 2;
        if ($bet->result == ExactScores::ThreeThree->value)
            return $awayScore == 3 && $homeScore == 2;
        if ($bet->result == ExactScores::AnyOtherAwayWin->value)
            return $awayScore > 3 && $awayScore > $homeScore;
        if ($bet->result == ExactScores::AnyOtherDraw->value)
            return $awayScore > 3 && $awayScore == $homeScore;
        if ($bet->result == ExactScores::AnyOtherHomeWin->value)
            return $homeScore > 3 && $homeScore > $awayScore;
        return false;
    }

    /**
     * check if a Bet on exact goals Wins
     */
    public function exactGoals(): bool
    {
        $game = $this->game;
        $bet = $this->bet;
        $score = $game->getScores($bet->gameTime, $bet->team);
        if ($bet->result == Goals::SevenOrMore->value)
            return $score > 6;
        return $score == (int) $bet->result;
    }

    /**
     * check if a Bet on even Odd Result goals Wins
     */
    public function evenOddResult(): bool
    {
        $game = $this->game;
        $bet = $this->bet;
        $score = $game->getScores($bet->gameTime, $bet->team);
        if ($bet->boolOutcome) {
            return $score % 2 == 0;
        }
        return $score % 2 != 0;
    }

    /**
     * check if a Bet on even asian Handicap Wins
     */
    public function asianHandicap(): bool
    {
        $game = $this->game;
        $bet = $this->bet;
        if ($bet->gameTime == ResultTime::secondHalf) return false;
        $awayScore  = $game->getScores($bet->gameTime, GoalCount::away);
        $homeScore =  $game->getScores($bet->gameTime, GoalCount::home);
        if ($bet->team == GoalCount::home) {
            $homeScore += $bet->result;
            if ($bet->boolOutcome) return $homeScore > $awayScore;
            return $homeScore <= $awayScore;
        }
        // GoalCount::away
        $awayScore +=  $bet->result;
        if ($bet->boolOutcome) return $awayScore > $homeScore;
        return $awayScore <= $homeScore;
    }

    /**
     * check if a Bet on both teams to score Wins
     */
    public function bothTeamsToScore(): bool
    {
        $game = $this->game;
        $bet = $this->bet;
        $homeScore = $game->getScores($bet->gameTime, GoalCount::home);
        $awayScore = $game->getScores($bet->gameTime, GoalCount::away);
        if ($bet->boolOutcome) return $homeScore > 0 && $awayScore > 0;
        return $homeScore == 0 || $awayScore == 0;
    }

    /**
     * check if a Bet on clean sheet Wins
     */
    public function cleanSheet(): bool
    {
        $game = $this->game;
        $bet = $this->bet;
        $homeScore = $game->getScores($bet->gameTime, GoalCount::home);
        $awayScore = $game->getScores($bet->gameTime, GoalCount::away);
        if ($bet->result == BothHalfsWinner::home->value) {
            return $awayScore == 0 && $homeScore > 0;
        }
        if ($bet->result == BothHalfsWinner::away->value) {
            return $homeScore == 0 && $awayScore > 0;
        }
        if ($bet->result == BothHalfsWinner::none->value) {
            return $awayScore > 0 && $homeScore > 0;
        }
        return $awayScore == 0 && $homeScore == 0;
    }
}
