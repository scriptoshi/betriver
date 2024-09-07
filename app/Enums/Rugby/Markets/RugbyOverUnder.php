<?php

namespace App\Enums\Rugby\Markets;

use App\Contracts\BetMarket;
use App\Enums\MarketCategory;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Rugby\Outcomes\RugbyOverUnderOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum RugbyOverUnder: string implements BetMarket
{
    case FULL_GAME = 'full_game';
    case FIRST_HALF = 'first_half';
    case SECOND_HALF = 'second_half';

    public function oddsId(): int
    {
        return match ($this) {
            self::FULL_GAME => 24,
            self::FIRST_HALF => 23,
            self::SECOND_HALF => 25,
        };
    }

    public function outcomes(): array
    {
        return RugbyOverUnderOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::FULL_GAME => "Over/Under",
            self::FIRST_HALF => "Over/Under 1st Half",
            self::SECOND_HALF => "Over/Under 2nd Half",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = RugbyOverUnderOutcome::from($bet->result);
        $period = match ($this) {
            self::FULL_GAME => 'fulltime',
            self::FIRST_HALF => 'halftime',
            self::SECOND_HALF => 'secondhalf',
        };

        $totalScore = $game->getScores($period, GoalCount::HOME) + $game->getScores($period, GoalCount::AWAY);

        return match ($outcome->type()) {
            'over' => $totalScore > $outcome->threshold(),
            'under' => $totalScore < $outcome->threshold(),
        };
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'sport' => LeagueSport::RUGBY
                ],
                [
                    'slug' => Str::slug(LeagueSport::RUGBY->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'category' => MarketCategory::getCategory(self::class),
                    'name' => $case->name(),
                    'sport' => LeagueSport::RUGBY,
                    'type' => EnumsMarket::RUGBY_OVER_UNDER
                ]
            );
            foreach (RugbyOverUnderOutcome::cases() as $outcome) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'result' => $outcome->value,
                    ],
                    ['name' => $outcome->name(), 'sport' => LeagueSport::RUGBY]
                );
            }
        }
    }
}
