<?php

namespace App\Enums\Hockey\Markets;

use App\Contracts\BetMarket;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Hockey\Outcomes\WinningMarginOutcome;
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
            self::HOME => 144,
            self::AWAY => 145,
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
        $homeScore = $game->getScores('total', GoalCount::HOME);
        $awayScore = $game->getScores('total', GoalCount::AWAY);
        $margin = $this === self::HOME ? $homeScore - $awayScore : $awayScore - $homeScore;

        return match ($outcome) {
            WinningMarginOutcome::BY_1 => $margin === 1,
            WinningMarginOutcome::BY_2 => $margin === 2,
            WinningMarginOutcome::BY_3 => $margin === 3,
            WinningMarginOutcome::BY_4_OR_MORE => $margin >= 4,
        };
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' => EnumsMarket::HOCKEY_WINNING_MARGIN,
                    'sport' => LeagueSport::HOCKEY
                ],
                [
                    'slug' => Str::slug(LeagueSport::HOCKEY->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'name' => self::formatMarketName($case->name()),
                    'sport' => LeagueSport::HOCKEY
                ]
            );

            foreach (WinningMarginOutcome::cases() as $outcome) {
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
    }

    private static function formatMarketName(string $name): string
    {
        return Str::of($name)->replace(['Home', 'Away'], ['{home}', '{away}']);
    }
}
