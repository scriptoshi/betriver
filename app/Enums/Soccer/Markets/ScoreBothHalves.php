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

enum ScoreBothHalves: string implements BetMarket
{
    case HOME = 'home';
    case AWAY = 'away';

    public function oddsId(): int
    {
        return match ($this) {
            self::HOME => 111,
            self::AWAY => 112,
        };
    }

    public function outcomes(): array
    {
        return YesNo::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::HOME => "Home will score in both halves",
            self::AWAY => "Away will score in both halves",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = YesNo::from($bet->result);
        $scoredFirstHalf = $this->scoredInHalf($game, 'halftime');
        $scoredSecondHalf = $this->scoredInHalf($game, 'secondhalf');
        return match ($outcome) {
            YesNo::YES => $scoredFirstHalf && $scoredSecondHalf,
            YesNo::NO => !($scoredFirstHalf && $scoredSecondHalf),
        };
    }

    private function scoredInHalf(Game $game, string $half): bool
    {
        $goals = $game->getScores($half, match ($this) {
            self::HOME => GoalCount::HOME,
            self::AWAY => GoalCount::AWAY,
        });
        return $goals > 0;
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' => EnumsMarket::SCORE_BOTH_HALVES,
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
