<?php

namespace App\Enums\Handball\Markets;

use App\Contracts\BetMarket;
use App\Enums\Handball\Outcomes\TeamGoalsOutcome;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum TeamGoals: string implements BetMarket
{
    case HOME_FULL_TIME = 'home_total';
    case AWAY_FULL_TIME = 'away_total';
    case HOME_FIRST_HALF = 'home_firsthalf';
    case AWAY_FIRST_HALF = 'away_firsthalf';
    case HOME_SECOND_HALF = 'home_secondhalf';
    case AWAY_SECOND_HALF = 'away_secondhalf';

    public function oddsId(): int
    {
        return match ($this) {
            self::HOME_FULL_TIME => 13,
            self::AWAY_FULL_TIME => 14,
            self::HOME_FIRST_HALF => 24,
            self::AWAY_FIRST_HALF => 25,
            self::HOME_SECOND_HALF => 32,
            self::AWAY_SECOND_HALF => 33,
        };
    }

    public function outcomes(): array
    {
        return TeamGoalsOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::HOME_FULL_TIME => "Total - Home",
            self::AWAY_FULL_TIME => "Total - Away",
            self::HOME_FIRST_HALF => "Home Team Total Goals(1st Half)",
            self::AWAY_FIRST_HALF => "Away Team Total Goals(1st Half)",
            self::HOME_SECOND_HALF => "Home Team Total Goals(2nd Half)",
            self::AWAY_SECOND_HALF => "Away Team Total Goals(2nd Half)",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = TeamGoalsOutcome::from($bet->result);

        [$team, $period] = explode('_', $this->value);
        $goals = $game->getScores($period, $team);

        return match ($outcome->type()) {
            'over' => $goals > $outcome->threshold(),
            'under' => $goals < $outcome->threshold(),
        };
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' => EnumsMarket::HANDBALL_TEAM_GOALS,
                    'sport' => LeagueSport::HANDBALL,
                ],
                [
                    'slug' => Str::slug(LeagueSport::HANDBALL->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'name' => formatName($case->name()),
                ]
            );

            foreach (TeamGoalsOutcome::cases() as $outcome) {
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
