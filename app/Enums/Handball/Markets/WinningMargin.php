<?php

namespace App\Enums\Handball\Markets;

use App\Enums\MarketCategory;
use App\Contracts\BetMarket;
use App\Enums\Handball\Outcomes\WinningMarginOutcome;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum WinningMargin: string implements BetMarket
{
    case HOME = 'home';
    case AWAY = 'away';

    public function oddsId(): int
    {
        return match ($this) {
            self::HOME => 67,
            self::AWAY => 68,
        };
    }

    public function outcomes(): array
    {
        return array_filter(WinningMarginOutcome::cases(), fn($outcome) => $outcome->team() == $this->value);
    }

    public function name(): string
    {
        return match ($this) {
            self::HOME => "Home Winning Margin",
            self::AWAY => "Away Winning Margin",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = WinningMarginOutcome::from($bet->result);

        $homeScore = $game->getScores('total', 'home');
        $awayScore = $game->getScores('total', 'away');

        $margin = match ($outcome->team()) {
            'home' => $homeScore - $awayScore,
            'away' => $awayScore - $homeScore,
        };

        if ($margin <= 0) {
            return false;
        }

        if ($outcome->maxMargin() == null) {
            return $margin >= $outcome->minMargin();
        }

        return $margin >= $outcome->minMargin() && $margin <= $outcome->maxMargin();
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' => EnumsMarket::HANDBALL_WINNING_MARGIN,
                    'sport' => LeagueSport::HANDBALL,
                ],
                [
                    'slug' => Str::slug(LeagueSport::HANDBALL->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'category' => MarketCategory::getCategory(self::class),
                    'name' => $case->name(),
                ]
            );

            foreach ($case->outcomes() as $outcome) {
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
