<?php

namespace App\Enums\Nfl\Markets;

use App\Contracts\BetMarket;
use App\Enums\MarketCategory;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Afl\Outcomes\AFLHighestScoringOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum NFLHighestScoring: string implements BetMarket
{
    case HALF = 'half';
    case QUARTER = 'quarter';

    public function oddsId(): int
    {
        return match ($this) {
            self::HALF => 31,
            self::QUARTER => 41,
        };
    }

    public function outcomes(): array
    {
        return match ($this) {
            self::HALF => [
                AFLHighestScoringOutcome::FIRST_HALF,
                AFLHighestScoringOutcome::SECOND_HALF,
                AFLHighestScoringOutcome::EQUAL,
            ],
            self::QUARTER => [
                AFLHighestScoringOutcome::FIRST_QUARTER,
                AFLHighestScoringOutcome::SECOND_QUARTER,
                AFLHighestScoringOutcome::THIRD_QUARTER,
                AFLHighestScoringOutcome::FOURTH_QUARTER,
                AFLHighestScoringOutcome::EQUAL,
            ],
        };
    }

    public function name(): string
    {
        return match ($this) {
            self::HALF => "Highest Scoring Half",
            self::QUARTER => "Highest Scoring Quarter",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = AFLHighestScoringOutcome::from($bet->result);

        $scores = match ($this) {
            self::HALF => [
                'first_half' => $this->getHalfScore($game, 'halftime'),
                'second_half' => $this->getHalfScore($game, 'secondhalf'),
            ],
            self::QUARTER => [
                'first_quarter' => $this->getQuarterScore($game, 'firstquarter'),
                'second_quarter' => $this->getQuarterScore($game, 'secondquarter'),
                'third_quarter' => $this->getQuarterScore($game, 'thirdquarter'),
                'fourth_quarter' => $this->getQuarterScore($game, 'fourthquarter'),
            ],
        };

        $highestScore = max($scores);
        $highestScoringPeriods = array_keys($scores, $highestScore);

        if (count($highestScoringPeriods) > 1) {
            return $outcome == AFLHighestScoringOutcome::EQUAL;
        }

        $highestScoringPeriod = $highestScoringPeriods[0];
        return $outcome->value == $highestScoringPeriod;
    }

    private function getHalfScore(Game $game, string $period): int
    {
        return $game->getScores($period, GoalCount::HOME) + $game->getScores($period, GoalCount::AWAY);
    }

    private function getQuarterScore(Game $game, string $period): int
    {
        return $game->getScores($period, GoalCount::HOME) + $game->getScores($period, GoalCount::AWAY);
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'sport' => LeagueSport::NFL
                ],
                [
                    'slug' => Str::slug(LeagueSport::NFL->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'category' => MarketCategory::getCategory(self::class),
                    'name' => $case->name(),
                    'type' => EnumsMarket::NFL_HIGHEST_SCORING,
                    'sport' => LeagueSport::NFL
                ]
            );
            foreach ($case->outcomes() as $outcome) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'result' => $outcome->value,
                    ],
                    ['name' => $outcome->name(), 'sport' => LeagueSport::NFL]
                );
            }
        }
    }
}
