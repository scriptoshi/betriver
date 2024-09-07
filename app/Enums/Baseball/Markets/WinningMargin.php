<?php

namespace App\Enums\Baseball\Markets;

use App\Contracts\BetMarket;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Baseball\Outcomes\WinningMarginOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;
use App\Enums\MarketCategory;

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
        return WinningMarginOutcome::cases();
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
        $margin = $this === self::HOME ? $homeScore - $awayScore : $awayScore - $homeScore;

        return match ($outcome) {
            WinningMarginOutcome::ONE => $margin === 1,
            WinningMarginOutcome::TWO => $margin === 2,
            WinningMarginOutcome::THREE => $margin === 3,
            WinningMarginOutcome::FOUR => $margin === 4,
            WinningMarginOutcome::FIVE_PLUS => $margin >= 5,
        };
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' => EnumsMarket::BASEBALL_WINNING_MARGIN,
                    'sport' => LeagueSport::BASEBALL,
                ],
                [
                    'slug' => Str::slug($case->name()),
                    'description' => $case->name(),
                    'category' => MarketCategory::getCategory(self::class),
                    'name' => self::formatMarketName($case->name()),
                    'sport' => LeagueSport::BASEBALL,
                ]
            );

            foreach (WinningMarginOutcome::cases() as $outcome) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'result' => $outcome->value,
                    ],
                    ['name' => $outcome->name(), 'sport' => LeagueSport::BASEBALL]
                );
            }
        }
    }

    private static function formatMarketName(string $name): string
    {
        return Str::of($name)->replace(['Home', 'Away'], ['{home}', '{away}']);
    }
}
