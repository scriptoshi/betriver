<?php

namespace App\Enums\Soccer\Markets;

use App\Contracts\BetMarket;

use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Soccer\Outcomes\YesNo;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum WinBothHalves: string implements BetMarket
{
    case HOME = 'home';
    case AWAY = 'away';

    public function oddsId(): int
    {
        return match ($this) {
            self::HOME => 37,
            self::AWAY => 53,
        };
    }

    public function outcomes(): array
    {
        return YesNo::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::HOME => "Home win both halves",
            self::AWAY => "Away win both halves",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = YesNo::from($bet->result);
        $firstHalfWon = $this->wonHalf($game, 'halftime');
        $secondHalfWon = $this->wonHalf($game, 'secondhalf');

        return match ($outcome) {
            YesNo::YES => $firstHalfWon && $secondHalfWon,
            YesNo::NO => !($firstHalfWon && $secondHalfWon),
        };
    }

    private function wonHalf(Game $game, string $half): bool
    {
        $homeGoals = $game->getScores($half, GoalCount::HOME);
        $awayGoals = $game->getScores($half, GoalCount::AWAY);

        return match ($this) {
            self::HOME => $homeGoals > $awayGoals,
            self::AWAY => $awayGoals > $homeGoals,
        };
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' => EnumsMarket::WIN_BOTH_HALVES,
                    'sport' => LeagueSport::FOOTBALL,
                ],
                [
                    'slug' => Str::slug($case->name()),
                    'description' => $case->name(),
                    'name' => self::formatMarketName($case->name()),
                    'sport' => LeagueSport::FOOTBALL

                ]
            );

            foreach (YesNo::cases() as $outcome) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'result' => $outcome->value,
                    ],
                    ['name' => $outcome->name(), 'sport' => LeagueSport::FOOTBALL]
                );
            }
        }
    }

    private static function formatMarketName(string $name): string
    {
        return Str::of($name)->replace(['Home', 'Away'], ['{home}', '{away}']);
    }
}
