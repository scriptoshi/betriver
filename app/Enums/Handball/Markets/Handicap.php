<?php

namespace App\Enums\Handball\Markets;

use App\Contracts\BetMarket;
use App\Enums\Handball\Outcomes\HandicapOutcome;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum Handicap: string implements BetMarket
{
    case FULL_TIME = 'total';
    case FIRST_HALF = 'firsthalf';
    case SECOND_HALF = 'secondhalf';

    public function oddsId(): int
    {
        return match ($this) {
            self::FULL_TIME => 4,
            self::FIRST_HALF => 15,
            self::SECOND_HALF => 23,
        };
    }

    public function outcomes(): array
    {
        return HandicapOutcome::cases();
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
        $outcome = HandicapOutcome::from($bet->result);
        $homeScore = $game->getScores($this->value, 'home');
        $awayScore = $game->getScores($this->value, 'away');

        $handicapValue = $outcome->handicapValue();
        $adjustedHomeScore = $homeScore + $handicapValue;

        $scoreDifference = $adjustedHomeScore - $awayScore;

        return match ($outcome->team()) {
            '{home}' => $scoreDifference > 0,
            '{away}' => $scoreDifference < 0,
        };
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' => EnumsMarket::HANDBALL_HANDICAP,
                    'sport' => LeagueSport::HANDBALL,
                ],
                [
                    'slug' => Str::slug(LeagueSport::HANDBALL->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'name' => $case->name(),
                ]
            );

            foreach (HandicapOutcome::cases() as $outcome) {
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
