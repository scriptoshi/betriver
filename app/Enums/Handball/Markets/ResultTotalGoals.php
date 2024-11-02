<?php

namespace App\Enums\Handball\Markets;

use App\Enums\MarketCategory;
use App\Contracts\BetMarket;
use App\Enums\Handball\Outcomes\ResultTotalGoalsOutcome;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum ResultTotalGoals: string implements BetMarket
{
    case FULL_TIME = 'total';
    case FIRST_HALF = 'firsthalf';

    public function oddsId(): int
    {
        return match ($this) {
            self::FULL_TIME => 29,
            self::FIRST_HALF => 30,
        };
    }

    public function outcomes(): array
    {
        return ResultTotalGoalsOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::FULL_TIME => "Result/Total Goals",
            self::FIRST_HALF => "Result/Total Goals (1st Half)",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = ResultTotalGoalsOutcome::from($bet->result);

        $homeScore = $game->getScores($this->value, 'home');
        $awayScore = $game->getScores($this->value, 'away');
        $totalGoals = $homeScore + $awayScore;

        $result = match (true) {
            $homeScore > $awayScore => 'home',
            $awayScore > $homeScore => 'away',
            default => 'draw',
        };

        $isCorrectResult = $result == $outcome->result();
        $isCorrectTotal = match ($outcome->overUnder()) {
            'over' => $totalGoals > $outcome->threshold(),
            'under' => $totalGoals < $outcome->threshold(),
        };

        return $isCorrectResult && $isCorrectTotal;
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' => EnumsMarket::HANDBALL_RESULT_TOTAL_GOALS,
                    'sport' => LeagueSport::HANDBALL,
                ],
                [
                    'slug' => Str::slug(LeagueSport::HANDBALL->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'category' => MarketCategory::getCategory(self::class),
                    'name' => $case->name(),
                ]
            );

            foreach (ResultTotalGoalsOutcome::cases() as $outcome) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'result' => $outcome->value,
                    ],
                    [
                        'name' => $outcome->name(),
                        'sport' => LeagueSport::HANDBALL,
                    ]
                );
            }
        }
    }
}
