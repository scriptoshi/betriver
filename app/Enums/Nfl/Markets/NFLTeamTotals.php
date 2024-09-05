<?php

namespace App\Enums\Nfl\Markets;

use App\Contracts\BetMarket;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Afl\Outcomes\AFLTeamTotalsOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum NFLTeamTotals: string implements BetMarket
{
    case HOME_FULL_TIME = 'home_full_time';
    case AWAY_FULL_TIME = 'away_full_time';
    case HOME_FIRST_HALF = 'home_first_half';
    case AWAY_FIRST_HALF = 'away_first_half';
    case HOME_SECOND_HALF = 'home_second_half';
    case AWAY_SECOND_HALF = 'away_second_half';
    case HOME_FIRST_QUARTER = 'home_first_quarter';
    case AWAY_FIRST_QUARTER = 'away_first_quarter';

    public function oddsId(): int
    {
        return match ($this) {
            self::HOME_FULL_TIME => 8,
            self::AWAY_FULL_TIME => 9,
            self::HOME_FIRST_HALF => 24,
            self::AWAY_FIRST_HALF => 25,
            self::HOME_SECOND_HALF => 60,
            self::AWAY_SECOND_HALF => 61,
            self::HOME_FIRST_QUARTER => 26,
            self::AWAY_FIRST_QUARTER => 27,
        };
    }

    public function outcomes(): array
    {
        return AFLTeamTotalsOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::HOME_FULL_TIME => "Total - {home}",
            self::AWAY_FULL_TIME => "Total - {away}",
            self::HOME_FIRST_HALF => "{home} Team Total Points (1st Half)",
            self::AWAY_FIRST_HALF => "{away} Team Total Points (1st Half)",
            self::HOME_SECOND_HALF => "{home} Team Total Points (2nd Half)",
            self::AWAY_SECOND_HALF => "{away} Team Total Points (2nd Half)",
            self::HOME_FIRST_QUARTER => "{home} Team Total Points (1st Quarter)",
            self::AWAY_FIRST_QUARTER => "{away} Team Total Points (1st Quarter)",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = AFLTeamTotalsOutcome::from($bet->result);

        $team = Str::startsWith($this->value, 'home') ? GoalCount::HOME : GoalCount::AWAY;
        $period = match ($this) {
            self::HOME_FULL_TIME, self::AWAY_FULL_TIME => 'fulltime',
            self::HOME_FIRST_HALF, self::AWAY_FIRST_HALF => 'halftime',
            self::HOME_SECOND_HALF, self::AWAY_SECOND_HALF => 'secondhalf',
            self::HOME_FIRST_QUARTER, self::AWAY_FIRST_QUARTER => 'firstquarter',
        };

        $score = $game->getScores($period, $team);

        return match ($outcome->type()) {
            'over' => $score > $outcome->value(),
            'under' => $score < $outcome->value(),
        };
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'sport' => LeagueSport::NFL
                ],
                [
                    'slug' => Str::slug(LeagueSport::NFL->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'name' => $case->name(),
                    'sport' => LeagueSport::NFL,
                    'type' => EnumsMarket::NFL_TEAM_TOTALS
                ]
            );
            foreach (AFLTeamTotalsOutcome::cases() as $outcome) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'result' => $outcome->value,
                    ],
                    ['name' => $outcome->name(), 'sport' => LeagueSport::NFL]
                );
            }
        }
    }
}
