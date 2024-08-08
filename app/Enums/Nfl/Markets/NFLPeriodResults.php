<?php

namespace App\Enums\Nfl\Markets;

use App\Contracts\BetMarket;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Afl\Outcomes\AFLPeriodResultsOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum NFLPeriodResults: string implements BetMarket
{
    case FIRST_HALF = 'first_half';
    case SECOND_HALF = 'second_half';
    case FIRST_QUARTER = 'first_quarter';
    case SECOND_QUARTER = 'second_quarter';
    case THIRD_QUARTER = 'third_quarter';
    case FOURTH_QUARTER = 'fourth_quarter';

    public function oddsId(): int
    {
        return match ($this) {
            self::FIRST_HALF => 22,
            self::SECOND_HALF => 37,
            self::FIRST_QUARTER => 23,
            self::SECOND_QUARTER => 38,
            self::THIRD_QUARTER => 39,
            self::FOURTH_QUARTER => 40,
        };
    }

    public function outcomes(): array
    {
        return AFLPeriodResultsOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::FIRST_HALF => "Home/Away - 1st Half",
            self::SECOND_HALF => "Home/Away - 2nd Half",
            self::FIRST_QUARTER => "Home/Away - 1st Qtr",
            self::SECOND_QUARTER => "Home/Away - 2nd Qtr",
            self::THIRD_QUARTER => "Home/Away - 3rd Qtr",
            self::FOURTH_QUARTER => "Home/Away - 4th Qtr",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = AFLPeriodResultsOutcome::from($bet->result);

        $period = match ($this) {
            self::FIRST_HALF => 'halftime',
            self::SECOND_HALF => 'secondhalf',
            self::FIRST_QUARTER => 'firstquarter',
            self::SECOND_QUARTER => 'secondquarter',
            self::THIRD_QUARTER => 'thirdquarter',
            self::FOURTH_QUARTER => 'fourthquarter',
        };

        $homeScore = $game->getScores($period, GoalCount::HOME);
        $awayScore = $game->getScores($period, GoalCount::AWAY);

        return match ($outcome) {
            AFLPeriodResultsOutcome::HOME => $homeScore > $awayScore,
            AFLPeriodResultsOutcome::AWAY => $awayScore > $homeScore,
            AFLPeriodResultsOutcome::DRAW => $homeScore === $awayScore,
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
                    'type' => EnumsMarket::NFL_PERIOD_RESULTS
                ]
            );
            foreach (AFLPeriodResultsOutcome::cases() as $outcome) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'name' => $outcome->name(),
                    ],
                    ['result' => $outcome->value, 'sport' => LeagueSport::NFL]
                );
            }
        }
    }
}
