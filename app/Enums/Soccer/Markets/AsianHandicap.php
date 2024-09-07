<?php

namespace App\Enums\Soccer\Markets;

use App\Contracts\BetMarket;
use App\Enums\MarketCategory;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Soccer\Outcomes\AsianHandicapOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum AsianHandicap: string implements BetMarket
{
    case FULL_TIME = 'full_time';
    case FIRST_HALF = 'first_half';
    case SECOND_HALF = 'second_half';

    public function oddsId(): int
    {
        return match ($this) {
            self::FULL_TIME => 4,
            self::FIRST_HALF => 19,
            self::SECOND_HALF => 104,
        };
    }

    public function outcomes(): array
    {
        return AsianHandicapOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::FULL_TIME => "Asian Handicap",
            self::FIRST_HALF => "Asian Handicap First Half",
            self::SECOND_HALF => "Asian Handicap (2nd Half)",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = AsianHandicapOutcome::from($bet->result);
        $period = match ($this) {
            self::FULL_TIME => 'fulltime',
            self::FIRST_HALF => 'halftime',
            self::SECOND_HALF => 'secondhalf',
        };

        $homeGoals = $game->getScores($period, GoalCount::HOME);
        $awayGoals = $game->getScores($period, GoalCount::AWAY);

        $handicapValue = $outcome->handicapValue();
        $adjustedHomeGoals = $homeGoals + $handicapValue;

        $goalDifference = $adjustedHomeGoals - $awayGoals;

        return match ($outcome->team()) {
            'home' => $goalDifference > 0,
            'away' => $goalDifference < 0,
        };
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'sport' => LeagueSport::FOOTBALL
                ],
                [
                    'slug' => Str::slug($case->name()),
                    'description' => $case->name(),
                    'category' => MarketCategory::getCategory(self::class),
                    'name' => self::formatMarketName($case->name()),
                    'type' => EnumsMarket::ASIAN_HANDICAP,
                ]
            );
            foreach (AsianHandicapOutcome::cases() as $outcome) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'result' => $outcome->value
                    ],
                    [
                        'name' => $outcome->name(),
                        'sport' => LeagueSport::FOOTBALL
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
