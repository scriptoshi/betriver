<?php

namespace App\Enums\Nfl\Markets;

use App\Contracts\BetMarket;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Nfl\Outcomes\NFLWinningMarginOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum NFLWinningMargin: string implements BetMarket
{
    case HOME_WINNING_MARGIN = 'home_winning_margin';
    case AWAY_WINNING_MARGIN = 'away_winning_margin';
    case HOME_WINNING_MARGIN_FIRST_HALF = 'home_winning_margin_first_half';
    case AWAY_WINNING_MARGIN_FIRST_HALF = 'away_winning_margin_first_half';

    public function oddsId(): int
    {
        return match ($this) {
            self::HOME_WINNING_MARGIN => 18,
            self::AWAY_WINNING_MARGIN => 19,
            self::HOME_WINNING_MARGIN_FIRST_HALF => 139,
            self::AWAY_WINNING_MARGIN_FIRST_HALF => 140,
        };
    }

    public function outcomes(): array
    {
        return NFLWinningMarginOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::HOME_WINNING_MARGIN => "{home} Winning Margin (14-Way)",
            self::AWAY_WINNING_MARGIN => "{away} Winning Margin (14-Way)",
            self::HOME_WINNING_MARGIN_FIRST_HALF => "{home} Winning Margin (1st Half)",
            self::AWAY_WINNING_MARGIN_FIRST_HALF => "{away} Winning Margin (1st Half)",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = NFLWinningMarginOutcome::from($bet->result);
        $period = $this->value === 'home_winning_margin_first_half' || $this->value === 'away_winning_margin_first_half' ? 'halftime' : 'fulltime';
        $homeScore = $game->getScores($period, GoalCount::HOME);
        $awayScore = $game->getScores($period, GoalCount::AWAY);

        $margin = $homeScore - $awayScore;
        if (Str::startsWith($this->value, 'away')) {
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
                    'sport' => LeagueSport::NFL
                ],
                [
                    'slug' => Str::slug(LeagueSport::NFL->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'name' => self::formatMarketName($case->name()),
                    'sport' => LeagueSport::NFL,
                    'type' => EnumsMarket::NFL_WINNING_MARGIN
                ]
            );
            foreach (NFLWinningMarginOutcome::cases() as $outcome) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'name' => $outcome->name(),
                    ],
                    ['result' => $outcome->value, 'sport' => LeagueSport::NFL]
                );
            }
        }
    }

    private static function formatMarketName(string $name): string
    {
        return Str::of($name)->replace(['Home', 'Away'], ['{home}', '{away}']);
    }
}
