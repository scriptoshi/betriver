<?php

namespace App\Enums\Handball\Markets;

use App\Contracts\BetMarket;
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
            self::HOME => 54,
            self::AWAY => 66,
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

        $firstHalfHome = $game->getScores('firsthalf', 'home');
        $firstHalfAway = $game->getScores('firsthalf', 'away');
        $secondHalfHome = $game->getScores('secondhalf', 'home');
        $secondHalfAway = $game->getScores('secondhalf', 'away');

        $wonFirstHalf = match ($this) {
            self::HOME => $firstHalfHome > $firstHalfAway,
            self::AWAY => $firstHalfAway > $firstHalfHome,
        };

        $wonSecondHalf = match ($this) {
            self::HOME => $secondHalfHome > $secondHalfAway,
            self::AWAY => $secondHalfAway > $secondHalfHome,
        };

        $wonBothHalves = $wonFirstHalf && $wonSecondHalf;

        return match ($outcome) {
            YesNo::YES => $wonBothHalves,
            YesNo::NO => !$wonBothHalves,
        };
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' => EnumsMarket::HANDBALL_WIN_BOTH_HALVES,
                    'sport' => LeagueSport::HANDBALL,
                ],
                [
                    'slug' => Str::slug(LeagueSport::HANDBALL->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'name' => $case->name(),
                ]
            );

            foreach (YesNo::cases() as $outcome) {
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
