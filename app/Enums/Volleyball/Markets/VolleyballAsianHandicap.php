<?php

namespace App\Enums\Volleyball\Markets;

use App\Contracts\BetMarket;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Volleyball\Outcomes\VolleyballAsianHandicapOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum VolleyballAsianHandicap: string implements BetMarket
{
    case FULL_MATCH = 'full_match';

    public function oddsId(): int
    {
        return 34; // Assuming 34 is the ID for "Asian Handicap"
    }

    public function outcomes(): array
    {
        return VolleyballAsianHandicapOutcome::cases();
    }

    public function name(): string
    {
        return "Asian Handicap";
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = VolleyballAsianHandicapOutcome::from($bet->result);
        $homeSets = $game->getScores('fulltime', GoalCount::HOME);
        $awaySets = $game->getScores('fulltime', GoalCount::AWAY);

        $handicapValue = $outcome->handicapValue();
        $adjustedHomeSets = $homeSets + $handicapValue;

        $setDifference = $adjustedHomeSets - $awaySets;

        return match ($outcome->team()) {
            'home' => $setDifference > 0,
            'away' => $setDifference < 0,
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
                    'name' => self::formatMarketName($case->name()),
                    'type' => EnumsMarket::VOLLEYBALL_ASIAN_HANDICAP,
                    'sport' => LeagueSport::VOLLEYBALL
                ]
            );
            foreach (VolleyballAsianHandicapOutcome::cases() as $outcome) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'name' => $outcome->name(),
                    ],
                    ['result' => $outcome->value, 'sport' => LeagueSport::VOLLEYBALL]
                );
            }
        }
    }

    private static function formatMarketName(string $name): string
    {
        return Str::of($name)->replace(['Home', 'Away'], ['{home}', '{away}']);
    }
}
