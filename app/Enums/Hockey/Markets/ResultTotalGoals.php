<?php

namespace App\Enums\Hockey\Markets;

use App\Enums\MarketCategory;
use App\Contracts\BetMarket;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Hockey\Outcomes\ResultTotalGoalsOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum ResultTotalGoals: string implements BetMarket
{
    case FULL_TIME = 'total';

    public function oddsId(): int
    {
        return 58;
    }

    public function outcomes(): array
    {
        return ResultTotalGoalsOutcome::cases();
    }

    public function name(): string
    {
        return "Result/Total Goals";
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = ResultTotalGoalsOutcome::from($bet->result);
        $homeScore = $game->getScores($this->value, GoalCount::HOME);
        $awayScore = $game->getScores($this->value, GoalCount::AWAY);
        $totalGoals = $homeScore + $awayScore;

        $result = match (true) {
            $homeScore > $awayScore => 'home',
            $awayScore > $homeScore => 'away',
            default => 'draw',
        };

        return $outcome->result() == $result && $outcome->isGoalRangeCorrect($totalGoals);
    }

    public static function seed(): void
    {
        $market = Market::updateOrCreate(
            [
                'segment' => self::FULL_TIME->value,
                'oddsId' => self::FULL_TIME->oddsId(),
                'type' => EnumsMarket::HOCKEY_RESULT_TOTAL_GOALS,
                'sport' => LeagueSport::HOCKEY
            ],
            [
                'slug' => Str::slug(LeagueSport::HOCKEY->value . '-' . self::FULL_TIME->name()),
                'description' => self::FULL_TIME->name(),
                'name' => self::formatMarketName(self::FULL_TIME->name()),
                'category' => MarketCategory::getCategory(self::class),
                'sport' => LeagueSport::HOCKEY
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
                    'sport' => LeagueSport::HOCKEY
                ]
            );
        }
    }

    private static function formatMarketName(string $name): string
    {
        return Str::of($name)->replace(['Home', 'Away'], ['{home}', '{away}']);
    }
}
