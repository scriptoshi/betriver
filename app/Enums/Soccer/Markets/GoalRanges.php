<?php

namespace App\Enums\Soccer\Markets;

use App\Contracts\BetMarket;
use App\Enums\MarketCategory;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Soccer\Outcomes\GoalRangesOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum GoalRanges: string implements BetMarket
{
    case FULLTIME = 'fulltime';
    case FIRSTHALF = 'firsthalf';

    public function oddsId(): int
    {
        return match ($this) {
            self::FULLTIME => 186,
            self::FIRSTHALF => 187,
        };
    }

    public function outcomes(): array
    {
        return GoalRangesOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::FULLTIME => "Total Goal Range",
            self::FIRSTHALF => "Total Goal Range (1st Half)",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = GoalRangesOutcome::from($bet->result);
        $totalGoals = $game->getScores($this->value, GoalCount::HOME) + $game->getScores($this->value, GoalCount::AWAY);
        return $outcome->isInRange($totalGoals);
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' => EnumsMarket::GOAL_RANGES,
                    'sport' => LeagueSport::FOOTBALL
                ],
                [
                    'slug' => Str::slug($case->name()),
                    'description' => $case->name(),
                    'category' => MarketCategory::getCategory(self::class),
                    'name' => self::formatMarketName($case->name()),
                    'sport' => LeagueSport::FOOTBALL
                ]
            );

            foreach (GoalRangesOutcome::cases() as $outcome) {
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
        return $name;
    }
}
