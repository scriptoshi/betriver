<?php

namespace App\Enums\MMA\Markets;

use App\Contracts\BetMarket;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\MMA\Outcomes\MMAOverUnderOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use App\Traits\MMAfights;
use Illuminate\Support\Str;

enum MMAOverUnder: string implements BetMarket
{

    use MMAfights;
    case OVER_UNDER = 'over_under';

    public function oddsId(): int
    {
        return match ($this) {
            self::OVER_UNDER => 4,
        };
    }

    public function outcomes(): array
    {
        return MMAOverUnderOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::OVER_UNDER => "Over/Under",
        };
    }

    public function won(Game $fight, Bet $bet): bool
    {
        if ($fight->sport != LeagueSport::MMA) return false;
        $outcome = MMAOverUnderOutcome::from($bet->result);
        $rounds =  static::completedRounds($fight);
        $lineValue = $outcome->lineValue();
        return match ($outcome->type()) {
            'over' => $rounds > $lineValue,
            'under' => $rounds < $lineValue,
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
                    'name' => self::formatMarketName($case->name()),
                    'type' => EnumsMarket::MMA_OVER_UNDER,
                ]
            );
            foreach (MMAOverUnderOutcome::cases() as $outcome) {
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
