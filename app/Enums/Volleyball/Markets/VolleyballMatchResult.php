<?php

namespace App\Enums\Volleyball\Markets;

use App\Enums\MarketCategory;
use App\Contracts\BetMarket;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Volleyball\Outcomes\VolleyballMatchResultOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum VolleyballMatchResult: string implements BetMarket
{
    case FULL_MATCH = 'full_match';

    public function oddsId(): int
    {
        return match ($this) {
            self::FULL_MATCH => 1, // Assuming 1 is the ID for "Home/Away" which is equivalent to Match Winner
        };
    }

    public function outcomes(): array
    {
        return VolleyballMatchResultOutcome::cases();
    }

    public function name(): string
    {
        return "Match Winner";
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = VolleyballMatchResultOutcome::from($bet->result);
        $homeSets = $game->getScores('fulltime', GoalCount::HOME);
        $awaySets = $game->getScores('fulltime', GoalCount::AWAY);

        return match ($outcome) {
            VolleyballMatchResultOutcome::HOME => $homeSets > $awaySets,
            VolleyballMatchResultOutcome::AWAY => $awaySets > $homeSets,
            VolleyballMatchResultOutcome::DRAW => $awaySets === $homeSets,
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
                    'type' => EnumsMarket::VOLLEYBALL_MATCH_RESULT,
                    'sport' => LeagueSport::VOLLEYBALL,
                    'is_default' => $case == self::FULL_MATCH,
                ]
            );
            foreach (VolleyballMatchResultOutcome::cases() as $outcome) {
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
