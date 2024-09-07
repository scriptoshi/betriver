<?php

namespace App\Enums\Handball\Markets;

use App\Enums\MarketCategory;
use App\Contracts\BetMarket;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Soccer\Outcomes\YesNo;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum WinEitherHalf: string implements BetMarket
{
    case HOME = 'home';
    case AWAY = 'away';

    public function oddsId(): int
    {
        return 56; // Assuming the same odds ID for both home and away
    }

    public function outcomes(): array
    {
        return YesNo::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::HOME => "Home To Win Either Half",
            self::AWAY => "Away To Win Either Half",
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

        $wonEitherHalf = $wonFirstHalf || $wonSecondHalf;

        return match ($outcome) {
            YesNo::YES => $wonEitherHalf,
            YesNo::NO => !$wonEitherHalf,
        };
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' => EnumsMarket::HANDBALL_WIN_EITHER_HALF,
                    'sport' => LeagueSport::HANDBALL,
                ],
                [
                    'slug' => Str::slug($case->name()),
                    'description' => $case->name(),
                    'category' => MarketCategory::getCategory(self::class),
                    'name' => $case->name(),
                ]
            );

            foreach (YesNo::cases() as $outcome) {
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
