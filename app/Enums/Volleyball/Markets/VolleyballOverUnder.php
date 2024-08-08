<?php

namespace App\Enums\Volleyball\Markets;

use App\Contracts\BetMarket;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Volleyball\Outcomes\VolleyballOverUnderOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum VolleyballOverUnder: string implements BetMarket
{
    case TOTAL_SETS = 'total_sets';
    case HOME_SETS = 'home_sets';
    case AWAY_SETS = 'away_sets';

    public function oddsId(): int
    {
        return match ($this) {
            self::TOTAL_SETS => 2, // Assuming 2 is the ID for "Over/Under"
            self::HOME_SETS => 5, // ID for "Total - Home"
            self::AWAY_SETS => 6, // ID for "Total - Away"
        };
    }

    public function outcomes(): array
    {
        return VolleyballOverUnderOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::TOTAL_SETS => "Over/Under Total Sets",
            self::HOME_SETS => "{home} Total Sets",
            self::AWAY_SETS => "{away} Total Sets",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = VolleyballOverUnderOutcome::from($bet->result);
        $totalSets = match ($this) {
            self::TOTAL_SETS => $game->getScores('fulltime', GoalCount::HOME) + $game->getScores('fulltime', GoalCount::AWAY),
            self::HOME_SETS => $game->getScores('fulltime', GoalCount::HOME),
            self::AWAY_SETS => $game->getScores('fulltime', GoalCount::AWAY),
        };

        return match ($outcome->type()) {
            'over' => $totalSets > $outcome->value(),
            'under' => $totalSets < $outcome->value(),
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
                    'type' => EnumsMarket::VOLLEYBALL_OVER_UNDER,
                    'sport' => LeagueSport::VOLLEYBALL
                ]
            );
            foreach (VolleyballOverUnderOutcome::cases() as $outcome) {
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
