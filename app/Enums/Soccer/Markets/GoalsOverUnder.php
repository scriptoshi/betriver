<?php

namespace App\Enums\Soccer\Markets;

use App\Contracts\BetMarket;

use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Soccer\Outcomes\GoalsOverUnderOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum GoalsOverUnder: string implements BetMarket
{
    case FULL_GAME = 'full_game';
    case FIRST_HALF = 'first_half';
    case SECOND_HALF = 'second_half';

    public function oddsId(): int
    {
        return match ($this) {
            self::FULL_GAME => 5,
            self::FIRST_HALF => 6,
            self::SECOND_HALF => 26,
        };
    }

    public function outcomes(): array
    {
        return GoalsOverUnderOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::FULL_GAME => "Goals Over/Under",
            self::FIRST_HALF => "Goals Over/Under First Half",
            self::SECOND_HALF => "Goals Over/Under - Second Half",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = GoalsOverUnderOutcome::from($bet->result);
        $period = match ($this) {
            self::FULL_GAME => 'fulltime',
            self::FIRST_HALF => 'halftime',
            self::SECOND_HALF => 'secondhalf',
        };

        $totalGoals = $game->getScores($period, GoalCount::HOME) + $game->getScores($period, GoalCount::AWAY);

        return match ($outcome->type()) {
            'over' => $totalGoals > $outcome->threshold(),
            'under' => $totalGoals < $outcome->threshold(),
        };
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'sport' => LeagueSport::FOOTBALL,
                ],
                [
                    'slug' => Str::slug($case->name()),
                    'description' => $case->name(),
                    'name' => self::formatMarketName($case->name()),
                    'type' => EnumsMarket::GOALS_OVER_UNDER,
                    'sport' => LeagueSport::FOOTBALL
                ]
            );
            foreach (GoalsOverUnderOutcome::cases() as $outcome) {
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
