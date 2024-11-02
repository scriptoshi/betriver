<?php

namespace App\Enums\Mma\Markets;

use App\Enums\MarketCategory;
use App\Contracts\BetMarket;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Mma\Outcomes\MMAMatchResultOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use App\Traits\MMAfights;
use Illuminate\Support\Str;

enum MMAMatchResult: string implements BetMarket
{
    use MMAfights;
    case THREE_WAY = 'three_way';
    case HOME_AWAY = 'home_away';

    public function oddsId(): int
    {
        return match ($this) {
            self::THREE_WAY => 1,
            self::HOME_AWAY => 2
        };
    }

    public function outcomes(): array
    {
        return match ($this) {
            self::THREE_WAY => [MMAMatchResultOutcome::HOME, MMAMatchResultOutcome::DRAW, MMAMatchResultOutcome::AWAY],
            self::HOME_AWAY => [MMAMatchResultOutcome::HOME, MMAMatchResultOutcome::AWAY],
        };
    }

    public function name(): string
    {
        return match ($this) {
            self::THREE_WAY => "3Way Result",
            self::HOME_AWAY => "Home/Away",
        };
    }

    public function won(Game $fight, Bet $bet): bool
    {
        $outcome = MMAMatchResultOutcome::from($bet->result);
        $winner = static::mmaFightWinner($fight);
        return match ($outcome) {
            MMAMatchResultOutcome::HOME => $winner == 'first',
            MMAMatchResultOutcome::AWAY => $winner == 'second',
            MMAMatchResultOutcome::DRAW => $winner == null
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
                    'name' => self::formatMarketName($case->name()),
                    'type' => EnumsMarket::MMA_MATCH_RESULT,
                    'is_default' => $case == self::THREE_WAY,
                ]
            );
            foreach ($case->outcomes() as $outcome) {
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

    private static function formatMarketName(string $name): string
    {
        return Str::of($name)->replace(['Home', 'Away'], ['{home}', '{away}']);
    }
}
