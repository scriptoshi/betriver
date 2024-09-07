<?php

namespace App\Enums\Rugby\Markets;

use App\Contracts\BetMarket;
use App\Enums\MarketCategory;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Soccer\Outcomes\YesNo;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum RugbyWinEitherHalf: string implements BetMarket
{
    case HOME = 'home';
    case AWAY = 'away';

    public function oddsId(): int
    {
        return match ($this) {
            self::HOME => 54,
            self::AWAY => 55,
        };
    }

    public function outcomes(): array
    {
        return YesNo::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::HOME => "{home} team will win either half",
            self::AWAY => "{away} team will win either half",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = YesNo::from($bet->result);
        $firstHalfWon = $this->wonHalf($game, 'halftime');
        $secondHalfWon = $this->wonHalf($game, 'secondhalf');

        $wonEitherHalf = $firstHalfWon || $secondHalfWon;

        return match ($outcome) {
            YesNo::YES => $wonEitherHalf,
            YesNo::NO => !$wonEitherHalf,
        };
    }

    private function wonHalf(Game $game, string $half): bool
    {
        $homeScore = $game->getScores($half, GoalCount::HOME);
        $awayScore = $game->getScores($half, GoalCount::AWAY);

        return match ($this) {
            self::HOME => $homeScore > $awayScore,
            self::AWAY => $awayScore > $homeScore,
        };
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'sport' => LeagueSport::RUGBY
                ],
                [
                    'slug' => Str::slug(LeagueSport::RUGBY->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'category' => MarketCategory::getCategory(self::class),
                    'name' => self::formatMarketName($case->name()),
                    'sport' => LeagueSport::RUGBY,
                    'type' => EnumsMarket::RUGBY_WIN_EITHER_HALF
                ]
            );
            foreach (YesNo::cases() as $outcome) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'result' => $outcome->value,
                    ],
                    ['name' => $outcome->name(), 'sport' => LeagueSport::RUGBY]
                );
            }
        }
    }

    private static function formatMarketName(string $name): string
    {
        return Str::of($name)->replace(['Home', 'Away'], ['{home}', '{away}']);
    }
}
