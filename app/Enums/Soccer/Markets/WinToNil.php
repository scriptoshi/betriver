<?php

namespace App\Enums\Soccer\Markets;

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

enum WinToNil: string implements BetMarket
{
    case HOME_FULL = 'home_full';
    case AWAY_FULL = 'away_full';
    case HOME_FIRST_HALF = 'home_first_half';
    case HOME_SECOND_HALF = 'home_second_half';
    case AWAY_FIRST_HALF = 'away_first_half';
    case AWAY_SECOND_HALF = 'away_second_half';

    public function oddsId(): int
    {
        return match ($this) {
            self::HOME_FULL => 29,
            self::AWAY_FULL => 30,
            self::HOME_FIRST_HALF => 199,
            self::HOME_SECOND_HALF => 200,
            self::AWAY_FIRST_HALF => 283,
            self::AWAY_SECOND_HALF => 284,
        };
    }

    public function outcomes(): array
    {
        return YesNo::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::HOME_FULL => "Win to Nil - Home",
            self::AWAY_FULL => "Win to Nil - Away",
            self::HOME_FIRST_HALF => "Home Win To Nil (1st Half)",
            self::HOME_SECOND_HALF => "Home Win To Nil (2nd Half)",
            self::AWAY_FIRST_HALF => "Away Win To Nil (1st Half)",
            self::AWAY_SECOND_HALF => "Away Win To Nil (2nd Half)",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = YesNo::from($bet->result);
        $result = match ($this) {
            self::HOME_FULL => $this->checkWinToNil($game, 'fulltime', 'home'),
            self::AWAY_FULL => $this->checkWinToNil($game, 'fulltime', 'away'),
            self::HOME_FIRST_HALF => $this->checkWinToNil($game, 'halftime', 'home'),
            self::HOME_SECOND_HALF => $this->checkWinToNil($game, 'secondhalf', 'home'),
            self::AWAY_FIRST_HALF => $this->checkWinToNil($game, 'halftime', 'away'),
            self::AWAY_SECOND_HALF => $this->checkWinToNil($game, 'secondhalf', 'away'),
        };
        return match ($outcome) {
            YesNo::YES => $result,
            YesNo::NO => !$result,
        };
    }

    private function checkWinToNil(Game $game, string $period, string $team): bool
    {
        $homeGoals = $game->getScores($period, GoalCount::HOME);
        $awayGoals = $game->getScores($period, GoalCount::AWAY);
        return match ($team) {
            'home' => $homeGoals > 0 && $awayGoals == 0,
            'away' => $awayGoals > 0 && $homeGoals == 0,
        };
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' => EnumsMarket::WIN_TO_NIL,
                    'sport' => LeagueSport::FOOTBALL,
                ],
                [
                    'slug' => Str::slug($case->name()),
                    'description' => $case->name(),
                    'category' => MarketCategory::getCategory(self::class),
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
