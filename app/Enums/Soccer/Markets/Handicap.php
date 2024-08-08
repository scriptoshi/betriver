<?php

namespace App\Enums\Soccer\Markets;

use App\Contracts\BetMarket;

use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Soccer\Outcomes\HandicapOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum Handicap: string implements BetMarket
{
    case FULL_TIME = 'full_time';
    case FIRST_HALF = 'first_half';

    public function oddsId(): int
    {
        return match ($this) {
            self::FULL_TIME => 9,
            self::FIRST_HALF => 18,
        };
    }

    public function outcomes(): array
    {
        return HandicapOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::FULL_TIME => "Handicap {home}/{away} (3 Way)",
            self::FIRST_HALF => "First Half Handicap {home}/{away} (3 Way)",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = HandicapOutcome::from($bet->result);
        $period = $this === self::FULL_TIME ? 'fulltime' : 'halftime';

        $homeGoals = $game->getScores($period, GoalCount::HOME);
        $awayGoals = $game->getScores($period, GoalCount::AWAY);

        $handicapValue = $outcome->handicapValue();
        $adjustedHomeGoals = $homeGoals + $handicapValue;

        $result = match (true) {
            $adjustedHomeGoals > $awayGoals => 'home',
            $adjustedHomeGoals < $awayGoals => 'away',
            default => 'draw',
        };

        return $outcome->result() === $result;
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' => EnumsMarket::HANDICAP,
                    'sport' => LeagueSport::FOOTBALL,
                ],
                [
                    'slug' => Str::slug($case->name()),
                    'description' => $case->name(),
                    'name' => self::formatMarketName($case->name()),
                    'sport' => LeagueSport::FOOTBALL
                ]
            );
            foreach (HandicapOutcome::cases() as $outcome) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'name' => $outcome->name(),
                    ],
                    ['result' => $outcome->value, 'sport' => LeagueSport::FOOTBALL]
                );
            }
        }
    }

    private static function formatMarketName(string $name): string
    {
        return Str::of($name)->replace(['Home', 'Away'], ['{home}', '{away}']);
    }
}
