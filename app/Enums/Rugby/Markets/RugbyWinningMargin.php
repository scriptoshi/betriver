<?php

namespace App\Enums\Rugby\Markets;

use App\Contracts\BetMarket;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Rugby\Outcomes\RugbyWinningMarginOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum RugbyWinningMargin: string implements BetMarket
{
    case HOME_WINNING_MARGIN = 'home_winning_margin';
    case AWAY_WINNING_MARGIN = 'away_winning_margin';

    public function oddsId(): int
    {
        return match ($this) {
            self::HOME_WINNING_MARGIN => 46,
            self::AWAY_WINNING_MARGIN => 47,
        };
    }

    public function outcomes(): array
    {
        return match ($this) {
            self::HOME_WINNING_MARGIN => array_filter(RugbyWinningMarginOutcome::cases(), fn($outcome) => $outcome->team() === 'home'),
            self::AWAY_WINNING_MARGIN => array_filter(RugbyWinningMarginOutcome::cases(), fn($outcome) => $outcome->team() === 'away'),
        };
    }

    public function name(): string
    {
        return match ($this) {
            self::HOME_WINNING_MARGIN => "{home} Winning Margin",
            self::AWAY_WINNING_MARGIN => "{away} Winning Margin",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = RugbyWinningMarginOutcome::from($bet->result);
        $homeScore = $game->getScores('fulltime', GoalCount::HOME);
        $awayScore = $game->getScores('fulltime', GoalCount::AWAY);

        $margin = $homeScore - $awayScore;
        if ($outcome->team() === 'away') {
            $margin = -$margin;
        }

        if ($margin <= 0) {
            return false;
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
                    'sport' => LeagueSport::RUGBY
                ],
                [
                    'slug' => Str::slug(LeagueSport::RUGBY->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'name' => self::formatMarketName($case->name()),
                    'sport' => LeagueSport::RUGBY,
                    'type' => EnumsMarket::RUGBY_WINNING_MARGIN
                ]
            );
            foreach ($case->outcomes() as $outcome) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'result' => $outcome->value,
                    ],
                    ['name' => $outcome->name(), 'sport' => LeagueSport::RUGBY]
                );
            }
        }
    }

    private static function formatMarketName(string $name): string
    {
        return Str::of($name)->replace(['Home', 'Away'], ['{home}', '{away}']);
    }
}
