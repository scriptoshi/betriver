<?php

namespace App\Enums\Handball\Markets;

use App\Enums\MarketCategory;
use App\Contracts\BetMarket;
use App\Enums\Handball\Outcomes\ExactGoalsOutcome;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum ExactGoals: string implements BetMarket
{
    case TOTAL = 'total';
    case HOME = 'home';
    case AWAY = 'away';

    public function oddsId(): int
    {
        return match ($this) {
            self::TOTAL => 55,
            self::HOME => 57,
            self::AWAY => 58,
        };
    }

    public function outcomes(): array
    {
        return ExactGoalsOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::TOTAL => "Exact Goals Number",
            self::HOME => "{home} Exact Goals Number",
            self::AWAY => "{away} Exact Goals Number",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = ExactGoalsOutcome::from($bet->result);

        $goals = match ($this) {
            self::TOTAL => $game->getScores('total', 'home') + $game->getScores('total', 'away'),
            self::HOME => $game->getScores('total', 'home'),
            self::AWAY => $game->getScores('total', 'away'),
        };

        if ($outcome === ExactGoalsOutcome::SIX_OR_MORE) {
            return $goals >= 6;
        }

        return $goals === $outcome->value();
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' => EnumsMarket::HANDBALL_EXACT_GOALS,
                    'sport' => LeagueSport::HANDBALL,
                ],
                [
                    'slug' => Str::slug(LeagueSport::HANDBALL->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'category' => MarketCategory::getCategory(self::class),
                    'name' => $case->name(),
                ]
            );

            foreach (ExactGoalsOutcome::cases() as $outcome) {
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
