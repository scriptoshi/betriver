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

enum WinToNil: string implements BetMarket
{
    case WIN_TO_NIL_HOME = 'win_to_nil_home';
    case WIN_TO_NIL_AWAY = 'win_to_nil_away';
    case CLEAN_SHEET_HOME = 'clean_sheet_home';
    case CLEAN_SHEET_AWAY = 'clean_sheet_away';

    public function oddsId(): int
    {
        return match ($this) {
            self::WIN_TO_NIL_HOME => 44,
            self::WIN_TO_NIL_AWAY => 45,
            self::CLEAN_SHEET_HOME => 63,
            self::CLEAN_SHEET_AWAY => 64,
        };
    }

    public function outcomes(): array
    {
        return YesNo::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::WIN_TO_NIL_HOME => "Win to Nil - Home",
            self::WIN_TO_NIL_AWAY => "Win to Nil - Away",
            self::CLEAN_SHEET_HOME => "Clean Sheet - Home",
            self::CLEAN_SHEET_AWAY => "Clean Sheet - Away",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = YesNo::from($bet->result);

        $homeScore = $game->getScores('total', 'home');
        $awayScore = $game->getScores('total', 'away');

        $result = match ($this) {
            self::WIN_TO_NIL_HOME => $homeScore > 0 && $awayScore === 0,
            self::WIN_TO_NIL_AWAY => $awayScore > 0 && $homeScore === 0,
            self::CLEAN_SHEET_HOME => $awayScore === 0,
            self::CLEAN_SHEET_AWAY => $homeScore === 0,
        };

        return match ($outcome) {
            YesNo::YES => $result,
            YesNo::NO => !$result,
        };
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' => EnumsMarket::HANDBALL_WIN_TO_NIL,
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
