<?php

namespace App\Enums\Soccer\Markets;

use App\Contracts\BetMarket;
use App\Enums\MarketCategory;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Soccer\Outcomes\WinningMarginOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum WinningMargin: string implements BetMarket
{
    case FULLTIME = 'fulltime';

    public function oddsId(): int
    {
        return 47;
    }

    public function outcomes(): array
    {
        return WinningMarginOutcome::cases();
    }

    public function name(): string
    {
        return "Winning Margin";
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = WinningMarginOutcome::from($bet->result);
        $homeGoals = $game->getScores($this->value, GoalCount::HOME);
        $awayGoals = $game->getScores($this->value, GoalCount::AWAY);
        return match ($outcome) {
            WinningMarginOutcome::DRAW => $homeGoals == $awayGoals,
            WinningMarginOutcome::HOME_1 => $homeGoals == $awayGoals + 1,
            WinningMarginOutcome::HOME_2 => $homeGoals == $awayGoals + 2,
            WinningMarginOutcome::HOME_3PLUS => $homeGoals >= $awayGoals + 3,
            WinningMarginOutcome::AWAY_1 => $awayGoals == $homeGoals + 1,
            WinningMarginOutcome::AWAY_2 => $awayGoals == $homeGoals + 2,
            WinningMarginOutcome::AWAY_3PLUS => $awayGoals >= $homeGoals + 3,
        };
    }

    public static function seed(): void
    {
        $market = Market::updateOrCreate(
            [
                'segment' => self::FULLTIME->value,
                'oddsId' => self::FULLTIME->oddsId(),
                'type' => EnumsMarket::WINNING_MARGIN,
                'sport' => LeagueSport::FOOTBALL,
            ],
            [
                'slug' => Str::slug(self::FULLTIME->name()),
                'description' => self::FULLTIME->name(),
                'category' => MarketCategory::getCategory(self::class),
                'name' => self::formatMarketName(self::FULLTIME->name()),
                'sport' => LeagueSport::FOOTBALL

            ]
        );

        foreach (WinningMarginOutcome::cases() as $outcome) {
            Bet::updateOrCreate(
                [
                    'market_id' => $market->id,
                    'result' => $outcome->value,
                ],
                ['name' => $outcome->name(), 'sport' => LeagueSport::FOOTBALL]
            );
        }
    }

    private static function formatMarketName(string $name): string
    {
        return $name;
    }
}
