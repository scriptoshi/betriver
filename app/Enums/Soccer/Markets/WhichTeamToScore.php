<?php

namespace App\Enums\Soccer\Markets;

use App\Contracts\BetMarket;

use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Soccer\Outcomes\WhichTeamToScoreOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum WhichTeamToScore: string implements BetMarket
{
    case FIRST_HALF = 'first_half';
    case SECOND_HALF = 'second_half';
    case FULL_GAME = 'full_game';

    public function oddsId(): int
    {
        return match ($this) {
            self::FIRST_HALF => 201,
            self::SECOND_HALF => 202,
            self::FULL_GAME => 285,
        };
    }

    public function outcomes(): array
    {
        return WhichTeamToScoreOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::FIRST_HALF => "Which Team To Score In 1st Half",
            self::SECOND_HALF => "Which Team To Score In 2nd Half",
            self::FULL_GAME => "Which Team To Score (Goals)",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = WhichTeamToScoreOutcome::from($bet->result);
        $period = match ($this) {
            self::FIRST_HALF => 'halftime',
            self::SECOND_HALF => 'secondhalf',
            self::FULL_GAME => 'fulltime',
        };

        $homeScored = $game->getScores($period, GoalCount::HOME) > 0;
        $awayScored = $game->getScores($period, GoalCount::AWAY) > 0;

        return match ($outcome) {
            WhichTeamToScoreOutcome::HOME => $homeScored && !$awayScored,
            WhichTeamToScoreOutcome::AWAY => !$homeScored && $awayScored,
            WhichTeamToScoreOutcome::BOTH => $homeScored && $awayScored,
            WhichTeamToScoreOutcome::NONE => !$homeScored && !$awayScored,
        };
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' =>  EnumsMarket::WHICH_TEAM_TO_SCORE,
                    'sport' => LeagueSport::FOOTBALL,
                ],
                [
                    'slug' => Str::slug($case->name()),
                    'description' => $case->name(),
                    'name' => self::formatMarketName($case->name()),
                    'sport' => LeagueSport::FOOTBALL
                ]
            );
            foreach (WhichTeamToScoreOutcome::cases() as $outcome) {
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
