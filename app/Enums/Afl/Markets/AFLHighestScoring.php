<?php

namespace App\Enums\Afl\Markets;

use App\Contracts\BetMarket;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Afl\Outcomes\AFLHighestScoringOutcome;
use App\Enums\Afl\ScoreType;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;
use App\Enums\MarketCategory;

enum AFLHighestScoring: string implements BetMarket
{
    case HALF = 'half';
    case QUARTER = 'quarter';

    public function oddsId(): int
    {
        return match ($this) {
            self::HALF => 7,
            self::QUARTER => 33,
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
                'first_half' => $game->getScores(ScoreType::firsthalf(), GoalCount::TOTAL),
                'second_half' => $game->getScores(ScoreType::secondhalf(), GoalCount::TOTAL)
            ],
            self::QUARTER => [
                'first_quarter' => $game->getScores(ScoreType::firstquarter(), GoalCount::TOTAL),
                'second_quarter' => $game->getScores(ScoreType::secondquarter(), GoalCount::TOTAL),
                'third_quarter' => $game->getScores(ScoreType::thirdquarter(), GoalCount::TOTAL),
                'fourth_quarter' => $game->getScores(ScoreType::fourthquarter(), GoalCount::TOTAL),
            ],
        };

        $highestScore = max($scores);
        $highestScoringPeriods = array_keys($scores, $highestScore);

        if (count($highestScoringPeriods) > 1) {
            return $outcome === AFLHighestScoringOutcome::EQUAL;
        }

        $highestScoringPeriod = $highestScoringPeriods[0];
        return $outcome->value === $highestScoringPeriod;
    }


    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'sport' => LeagueSport::AFL
                ],
                [
                    'slug' => Str::slug(LeagueSport::AFL->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'name' => $case->name(),
                    'type' => EnumsMarket::AFL_HIGHEST_SCORING, // 'AFLHighestScoring',
                    'sport' => LeagueSport::AFL,
                    'category' => MarketCategory::getCategory(self::class),
                ]
            );
            foreach ($case->outcomes() as $outcome) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'result' => $outcome->value,
                    ],
                    ['name' => $outcome->name(), 'sport' => LeagueSport::AFL]
                );
            }
        }
    }
}
