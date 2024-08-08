<?php

namespace App\Enums\Handball\Markets;

use App\Contracts\BetMarket;
use App\Enums\Handball\Outcomes\OverUnderOutcome;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum OverUnder: string implements BetMarket
{
    case FULL_TIME = 'total';
    case FIRST_HALF = 'firsthalf';
    case SECOND_HALF = 'secondhalf';

    public function oddsId(): int
    {
        return match ($this) {
            self::FULL_TIME => 5,
            self::FIRST_HALF => 6,
            self::SECOND_HALF => 7,
        };
    }

    public function outcomes(): array
    {
        return OverUnderOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::FULL_TIME => "Over/Under",
            self::FIRST_HALF => "Over/Under 1st Half",
            self::SECOND_HALF => "Over/Under 2nd Half",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = OverUnderOutcome::from($bet->result);
        $totalScore = $game->getScores($this->value, 'home') + $game->getScores($this->value, 'away');

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
                    'type' => EnumsMarket::HANDBALL_OVER_UNDER,
                    'sport' => LeagueSport::HANDBALL,
                ],
                [
                    'slug' => Str::slug(LeagueSport::HANDBALL->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'name' => $case->name(),
                ]
            );

            foreach (OverUnderOutcome::cases() as $outcome) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'name' => $outcome->name(),
                    ],
                    [
                        'result' => $outcome->value,
                        'sport' => LeagueSport::HANDBALL,
                    ]
                );
            }
        }
    }
}
