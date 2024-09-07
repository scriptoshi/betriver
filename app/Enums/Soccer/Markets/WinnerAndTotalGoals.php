<?php

namespace App\Enums\Soccer\Markets;

use App\Contracts\BetMarket;
use App\Enums\MarketCategory;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Soccer\Outcomes\WinnerAndTotalGoalsOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum WinnerAndTotalGoals: string implements BetMarket
{
    case FULL_TIME = 'full_time';
    case HALF_TIME = 'half_time';
    case SECOND_HALF = 'second_half';

    public function oddsId(): int
    {
        return match ($this) {
            self::FULL_TIME => 25,
            self::HALF_TIME => 51,
            self::SECOND_HALF => 194,
        };
    }

    public function outcomes(): array
    {
        return WinnerAndTotalGoalsOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::FULL_TIME => "Result/Total Goals",
            self::HALF_TIME => "Halftime Result/Total Goals",
            self::SECOND_HALF => "Result/Total Goals (2nd Half)",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = WinnerAndTotalGoalsOutcome::from($bet->result);
        $period = match ($this) {
            self::FULL_TIME => 'fulltime',
            self::HALF_TIME => 'halftime',
            self::SECOND_HALF => 'secondhalf',
        };

        $homeGoals = $game->getScores($period, GoalCount::HOME);
        $awayGoals = $game->getScores($period, GoalCount::AWAY);
        $totalGoals = $homeGoals + $awayGoals;

        $result = match (true) {
            $homeGoals > $awayGoals => 'home',
            $awayGoals > $homeGoals => 'away',
            default => 'draw',
        };

        return $outcome->result() === $result && $this->isGoalRangeCorrect($totalGoals, $outcome->goalRange());
    }

    private function isGoalRangeCorrect(int $totalGoals, string $range): bool
    {
        return match ($range) {
            'under_1.5' => $totalGoals < 1.5,
            'over_1.5' => $totalGoals > 1.5,
            'under_2.5' => $totalGoals < 2.5,
            'over_2.5' => $totalGoals > 2.5,
            'under_3.5' => $totalGoals < 3.5,
            'over_3.5' => $totalGoals > 3.5,
            'under_4.5' => $totalGoals < 4.5,
            'over_4.5' => $totalGoals > 4.5,
        };
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' => EnumsMarket::WINNER_AND_TOTAL_GOALS,
                    'sport' => LeagueSport::FOOTBALL,
                ],
                [
                    'slug' => Str::slug($case->name()),
                    'description' => $case->name(),
                    'category' => MarketCategory::getCategory(self::class),
                    'name' => self::formatMarketName($case->name()),
                    'sport' => LeagueSport::FOOTBALL

                ]
            );
            foreach (WinnerAndTotalGoalsOutcome::cases() as $outcome) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'result' => $outcome->value,
                    ],
                    ['name' => $outcome->name(), 'sport' => LeagueSport::FOOTBALL]
                );
            }
        }
    }

    private static function formatMarketName(string $name): string
    {
        return Str::of($name)->replace(['Home', 'Away'], ['{home}', '{away}']);
    }
}
