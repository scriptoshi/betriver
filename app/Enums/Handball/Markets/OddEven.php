<?php

namespace App\Enums\Handball\Markets;

use App\Enums\MarketCategory;
use App\Contracts\BetMarket;
use App\Enums\Handball\Outcomes\OddEvenOutcome;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum OddEven: string implements BetMarket
{
    case FULL_TIME = 'total';
    case FIRST_HALF = 'firsthalf';
    case SECOND_HALF = 'secondhalf';
    case HOME = 'home';
    case AWAY = 'away';

    public function oddsId(): int
    {
        return match ($this) {
            self::FULL_TIME => 18,
            self::FIRST_HALF => 19,
            self::SECOND_HALF => 26,
            self::HOME => 20,
            self::AWAY => 21,
        };
    }

    public function outcomes(): array
    {
        return OddEvenOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::FULL_TIME => "Odd/Even",
            self::FIRST_HALF => "Odd/Even 1st Half",
            self::SECOND_HALF => "Odd/Even 2nd Half",
            self::HOME => "{home} Odd/Even",
            self::AWAY => "{away} Odd/Even",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = OddEvenOutcome::from($bet->result);
        $totalGoals = match ($this) {
            self::FULL_TIME, self::FIRST_HALF, self::SECOND_HALF =>
            $game->getScores($this->value, 'home') + $game->getScores($this->value, 'away'),
            self::HOME => $game->getScores('total', 'home'),
            self::AWAY => $game->getScores('total', 'away'),
        };

        return $outcome->isCorrect($totalGoals);
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' => EnumsMarket::HANDBALL_ODD_EVEN,
                    'sport' => LeagueSport::HANDBALL,
                ],
                [
                    'slug' => Str::slug(LeagueSport::HANDBALL->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'category' => MarketCategory::getCategory(self::class),
                    'name' => $case->name(),
                ]
            );

            foreach (OddEvenOutcome::cases() as $outcome) {
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
