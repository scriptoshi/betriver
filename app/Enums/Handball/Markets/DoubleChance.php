<?php

namespace App\Enums\Handball\Markets;

use App\Contracts\BetMarket;
use App\Enums\Handball\Outcomes\DoubleChanceOutcome;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum DoubleChance: string implements BetMarket
{
    case FULL_TIME = 'total';
    case FIRST_HALF = 'firsthalf';
    case SECOND_HALF = 'secondhalf';

    public function oddsId(): int
    {
        return match ($this) {
            self::FULL_TIME => 11,
            self::FIRST_HALF => 16,
            self::SECOND_HALF => 17,
        };
    }

    public function outcomes(): array
    {
        return DoubleChanceOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::FULL_TIME => "Double Chance",
            self::FIRST_HALF => "Double Chance - 1st Half",
            self::SECOND_HALF => "Double Chance - 2nd Half",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = DoubleChanceOutcome::from($bet->result);
        $homeScore = $game->getScores($this->value, 'home');
        $awayScore = $game->getScores($this->value, 'away');

        $result = match (true) {
            $homeScore > $awayScore => 'home',
            $awayScore > $homeScore => 'away',
            default => 'draw',
        };

        return $outcome->includes($result);
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' => EnumsMarket::HANDBALL_DOUBLE_CHANCE,
                    'sport' => LeagueSport::HANDBALL,
                ],
                [
                    'slug' => Str::slug(LeagueSport::HANDBALL->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'name' => $case->name(),
                ]
            );

            foreach (DoubleChanceOutcome::cases() as $outcome) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'result' => $outcome->value,
                    ],
                    [
                        'name' => $outcome->name(),
                        'sport' => LeagueSport::HANDBALL,
                    ]
                );
            }
        }
    }
}
