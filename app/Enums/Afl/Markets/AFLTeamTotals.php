<?php

namespace App\Enums\Afl\Markets;

use App\Contracts\BetMarket;
use App\Enums\Afl\Outcomes\AFLTeamTotalsOutcome;
use App\Enums\Afl\ScoreType;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum AFLTeamTotals: string implements BetMarket
{
    case HOME_FULL_TIME = 'home_full_time';
    case AWAY_FULL_TIME = 'away_full_time';
    case HOME_FIRST_HALF = 'home_first_half';
    case AWAY_FIRST_HALF = 'away_first_half';
    case HOME_SECOND_HALF = 'home_second_half';
    case AWAY_SECOND_HALF = 'away_second_half';

    public function oddsId(): int
    {
        return match ($this) {
            self::HOME_FULL_TIME => 24,
            self::AWAY_FULL_TIME => 25,
            self::HOME_FIRST_HALF => 37,
            self::AWAY_FIRST_HALF => 26,
            self::HOME_SECOND_HALF => 38,
            self::AWAY_SECOND_HALF => 34,
        };
    }

    public function outcomes(): array
    {
        return AFLTeamTotalsOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::HOME_FULL_TIME => "{home} Total Points",
            self::AWAY_FULL_TIME => "{away} Total Points",
            self::HOME_FIRST_HALF => "{home} Total Points (1st Half)",
            self::AWAY_FIRST_HALF => "{away} Total Points (1st Half)",
            self::HOME_SECOND_HALF => "{home} Total Points (2nd Half)",
            self::AWAY_SECOND_HALF => "{away} Total Points (2nd Half)",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = AFLTeamTotalsOutcome::from($bet->result);

        $team = Str::startsWith($this->value, 'home') ? GoalCount::HOME : GoalCount::AWAY;
        $period = match ($this) {
            self::HOME_FULL_TIME, self::AWAY_FULL_TIME => ScoreType::fulltime(),
            self::HOME_FIRST_HALF, self::AWAY_FIRST_HALF => ScoreType::firsthalf(),
            self::HOME_SECOND_HALF, self::AWAY_SECOND_HALF => ScoreType::secondhalf(),
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
                    'sport' => LeagueSport::AFL
                ],
                [
                    'slug' => Str::slug(LeagueSport::AFL->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'name' => $case->name(),
                    'sport' => LeagueSport::AFL,
                    'type' => EnumsMarket::AFL_TEAM_TOTALS //'AFLTeamTotals',
                ]
            );
            foreach (AFLTeamTotalsOutcome::cases() as $outcome) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'result' => $outcome->value,
                    ],
                    ['name' => $outcome->name(), 'sport' => LeagueSport::AFL]
                );
            }
        }
    }
}
