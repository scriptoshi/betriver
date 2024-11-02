<?php

namespace App\Enums\Volleyball\Markets;

use App\Enums\MarketCategory;
use App\Contracts\BetMarket;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Volleyball\Outcomes\VolleyballOddEvenOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum VolleyballOddEven: string implements BetMarket
{
    case TOTAL_SETS = 'total_sets';

    public function oddsId(): int
    {
        return 7; // Assuming 7 is the ID for "Odd/Even"
    }

    public function outcomes(): array
    {
        return VolleyballOddEvenOutcome::cases();
    }

    public function name(): string
    {
        return "Odd/Even Total Sets";
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = VolleyballOddEvenOutcome::from($bet->result);
        $totalSets = $game->getScores('fulltime', GoalCount::HOME) + $game->getScores('fulltime', GoalCount::AWAY);

        return match ($outcome) {
            VolleyballOddEvenOutcome::ODD => $totalSets % 2 !== 0,
            VolleyballOddEvenOutcome::EVEN => $totalSets % 2 == 0,
        };
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'sport' => LeagueSport::VOLLEYBALL
                ],
                [
                    'slug' => Str::slug(LeagueSport::VOLLEYBALL->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'category' => MarketCategory::getCategory(self::class),
                    'name' => self::formatMarketName($case->name()),
                    'type' => EnumsMarket::VOLLEYBALL_ODD_EVEN,
                    'sport' => LeagueSport::VOLLEYBALL
                ]
            );
            foreach (VolleyballOddEvenOutcome::cases() as $outcome) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'result' => $outcome->value,
                    ],
                    [
                        'name' => $outcome->name(),
                        'sport' => LeagueSport::VOLLEYBALL
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
