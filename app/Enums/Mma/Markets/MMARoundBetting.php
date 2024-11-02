<?php

namespace App\Enums\Mma\Markets;

use App\Enums\MarketCategory;
use App\Contracts\BetMarket;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Mma\Outcomes\MMARoundBettingOutcome;

use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use App\Traits\MMAfights;
use Illuminate\Support\Str;

enum MMARoundBetting: string implements BetMarket
{
    use MMAfights;
    case ROUND_BETTING = 'round_betting';
    case ROUND_BETTING_PLAYER = 'round_betting_player';

    public function oddsId(): int
    {
        return match ($this) {
            self::ROUND_BETTING => 6,
            self::ROUND_BETTING_PLAYER => 23
        };
    }

    public function outcomes(): array
    {
        return MMARoundBettingOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::ROUND_BETTING => "Round Betting",
            self::ROUND_BETTING_PLAYER => "Round Betting (Player)"
        };
    }

    public function won(Game $fight, Bet $bet): bool
    {
        $outcome = MMARoundBettingOutcome::from($bet->result);
        $winningRound = static::completedRounds($fight);
        $winner = static::mmaFightWinner($fight);
        return match ($this) {
            self::ROUND_BETTING => $outcome->round() == $winningRound,
            self::ROUND_BETTING_PLAYER => $outcome->round() == $winningRound && $outcome->player() == $winner,
        };
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'sport' => LeagueSport::MMA
                ],
                [
                    'slug' => Str::slug($case->name()),
                    'description' => $case->name(),
                    'category' => MarketCategory::getCategory(self::class),
                    'name' => formatName($case->name()),
                    'type' => EnumsMarket::MMA_ROUND_BETTING,
                ]
            );
            foreach (MMARoundBettingOutcome::cases() as $outcome) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'name' => $outcome->name(),
                        'sport' => LeagueSport::MMA
                    ],
                    ['result' => $outcome->value]
                );
            }
        }
    }
}
