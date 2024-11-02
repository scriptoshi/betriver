<?php

namespace App\Enums\Afl\Markets;

use App\Contracts\BetMarket;
use App\Enums\Afl\Outcomes\AFLPeriodResultsOutcome;
use App\Enums\Afl\ScoreType;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;
use App\Enums\MarketCategory;

enum AFLPeriodResults: string implements BetMarket
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
            self::FIRST_HALF => 27,
            self::SECOND_HALF => 28,
            self::FIRST_QUARTER => 29,
            self::SECOND_QUARTER => 30,
            self::THIRD_QUARTER => 31,
            self::FOURTH_QUARTER => 32,
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
            self::FIRST_HALF => ScoreType::firsthalf(),
            self::SECOND_HALF => ScoreType::secondhalf(),
            self::FIRST_QUARTER => ScoreType::firstquarter(),
            self::SECOND_QUARTER => ScoreType::secondquarter(),
            self::THIRD_QUARTER => ScoreType::thirdquarter(),
            self::FOURTH_QUARTER => ScoreType::fourthquarter(),
        };

        $homeScore = $game->getScores($period, GoalCount::HOME);
        $awayScore = $game->getScores($period, GoalCount::AWAY);

        return match ($outcome) {
            AFLPeriodResultsOutcome::HOME => $homeScore > $awayScore,
            AFLPeriodResultsOutcome::AWAY => $awayScore > $homeScore,
            AFLPeriodResultsOutcome::DRAW => $homeScore == $awayScore,
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
                    'category' => MarketCategory::getCategory(self::class),
                    'name' => $case->name(),
                    'sport' => LeagueSport::AFL,
                    'type' => EnumsMarket::AFL_PERIOD_RESULTS //'AFLPeriodResults',
                ]
            );
            foreach (AFLPeriodResultsOutcome::cases() as $outcome) {
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
