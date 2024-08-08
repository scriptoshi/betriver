<?php

namespace App\Enums\Soccer\Markets;

use App\Contracts\BetMarket;

use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Soccer\Outcomes\OddEvenOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum OddEven: string implements BetMarket
{
    case FULLTIME = 'fulltime';
    case FIRSTHALF = 'firsthalf';
    case SECONDHALF = 'secondhalf';
    case HOME = 'home';
    case AWAY = 'away';

    public function oddsId(): int
    {
        return match ($this) {
            self::FULLTIME => 21,
            self::FIRSTHALF => 22,
            self::SECONDHALF => 63,
            self::HOME => 23,
            self::AWAY => 60,
        };
    }

    public function outcomes(): array
    {
        return OddEvenOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::FULLTIME => "Odd/Even",
            self::FIRSTHALF => "Odd/Even - First Half",
            self::SECONDHALF => "Odd/Even - Second Half",
            self::HOME => "Home Odd/Even",
            self::AWAY => "Away Odd/Even",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = OddEvenOutcome::from($bet->result);
        $goals = $this->getGoals($game);
        return ($goals % 2 == 0) == ($outcome == OddEvenOutcome::EVEN);
    }

    private function getGoals(Game $game): int
    {
        return match ($this) {
            self::FULLTIME => $game->getScores('fulltime', GoalCount::HOME) + $game->getScores('fulltime', GoalCount::AWAY),
            self::FIRSTHALF => $game->getScores('halftime', GoalCount::HOME) + $game->getScores('halftime', GoalCount::AWAY),
            self::SECONDHALF => $game->getScores('secondhalf', GoalCount::HOME) + $game->getScores('secondhalf', GoalCount::AWAY),
            self::HOME => $game->getScores('fulltime', GoalCount::HOME),
            self::AWAY => $game->getScores('fulltime', GoalCount::AWAY),
        };
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' => EnumsMarket::ODD_EVEN,
                    'sport' => LeagueSport::FOOTBALL,
                ],
                [
                    'slug' => Str::slug($case->name()),
                    'description' => $case->name(),
                    'name' => self::formatMarketName($case->name()),
                    'sport' => LeagueSport::FOOTBALL

                ]
            );

            foreach (OddEvenOutcome::cases() as $outcome) {
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
