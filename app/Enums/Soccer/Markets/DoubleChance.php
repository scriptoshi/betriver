<?php

namespace App\Enums\Soccer\Markets;

use App\Contracts\BetMarket;


use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Soccer\Outcomes\DoubleChanceOutcomes;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum DoubleChance: string  implements BetMarket
{
    case FULLTIME = 'fulltime';
    case FIRSTHALF = 'halftime';
    case SECONDHALF = 'secondhalf';

    public function oddsId(): ?int
    {
        return match ($this) {
            self::FULLTIME => 12,
            self::FIRSTHALF => 20,
            self::SECONDHALF => 33,
        };
    }

    public function outcomes(): array
    {
        return DoubleChanceOutcomes::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::FULLTIME => "Double Chance",
            self::FIRSTHALF => "First Half Double Chance",
            self::SECONDHALF => "Second Half Double Chance"
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = DoubleChanceOutcomes::from($bet->result);
        $homeScore = $game->getScores($this->value, GoalCount::HOME);
        $awayScore = $game->getScores($this->value, GoalCount::AWAY);

        return match ($outcome) {
            DoubleChanceOutcomes::HOME_DRAW => $homeScore >= $awayScore,
            DoubleChanceOutcomes::HOME_AWAY => $homeScore != $awayScore,
            DoubleChanceOutcomes::DRAW_AWAY => $homeScore <= $awayScore,
        };
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' => EnumsMarket::DOUBLE_CHANCE,
                    'sport' => LeagueSport::FOOTBALL
                ],
                [
                    'slug' => Str::slug($case->name()),
                    'description' => $case->name(),
                    'name' => self::formatMarketName($case->name()),
                    'sport' => LeagueSport::FOOTBALL
                ]
            );
            foreach (DoubleChanceOutcomes::cases() as $bet) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'result' => $bet->value,
                    ],
                    ['name' => $bet->name(),  'sport' => LeagueSport::FOOTBALL]
                );
            }
        }
    }

    private static function formatMarketName(string $name): string
    {
        return Str::of($name)->replace(
            ['Home', 'Away', 'Draw'],
            ['{home}', '{away}', 'Draw']
        );
    }
}
