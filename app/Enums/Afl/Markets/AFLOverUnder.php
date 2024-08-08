<?php

namespace App\Enums\Afl\Markets;

use App\Contracts\BetMarket;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Afl\Outcomes\AFLOverUnderOutcome;
use App\Enums\Afl\ScoreType;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum AFLOverUnder: string implements BetMarket
{
    case FULL_TIME = 'full_time';
    case FIRST_HALF = 'first_half';
    case SECOND_HALF = 'second_half';
    case FIRST_QUARTER = 'first_quarter';
    case SECOND_QUARTER = 'second_quarter';
    case THIRD_QUARTER = 'third_quarter';
    case FOURTH_QUARTER = 'fourth_quarter';

    public function oddsId(): int
    {
        return match ($this) {
            self::FULL_TIME => 4,
            self::FIRST_HALF => 5,
            self::SECOND_HALF => 40,
            self::FIRST_QUARTER => 17,
            self::SECOND_QUARTER => 18,
            self::THIRD_QUARTER => 19,
            self::FOURTH_QUARTER => 20,
        };
    }

    public function outcomes(): array
    {
        return AFLOverUnderOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::FULL_TIME => "Over/Under",
            self::FIRST_HALF => "Over/Under 1st Half",
            self::SECOND_HALF => "Over/Under 2nd Half",
            self::FIRST_QUARTER => "Over/Under 1st Qtr",
            self::SECOND_QUARTER => "Over/Under 2nd Qtr",
            self::THIRD_QUARTER => "Over/Under 3rd Qtr",
            self::FOURTH_QUARTER => "Over/Under 4th Qtr",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = AFLOverUnderOutcome::from($bet->result);
        $period = match ($this) {
            self::FULL_TIME => ScoreType::fulltime(),
            self::FIRST_HALF => ScoreType::firsthalf(),
            self::SECOND_HALF => ScoreType::secondhalf(),
            self::FIRST_QUARTER => ScoreType::firstquarter(),
            self::SECOND_QUARTER => ScoreType::secondquarter(),
            self::THIRD_QUARTER => ScoreType::thirdquarter(),
            self::FOURTH_QUARTER => ScoreType::fourthquarter(),
        };
        $homeScore = $game->getScores($period, GoalCount::HOME);
        $awayScore = $game->getScores($period, GoalCount::AWAY);
        $totalScore = $homeScore + $awayScore;

        $lineValue = $outcome->lineValue();

        return match ($outcome->type()) {
            'over' => $totalScore > $lineValue,
            'under' => $totalScore < $lineValue,
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
                    'type' => EnumsMarket::AFL_OVER_UNDER //'AFLOverUnder',
                ]
            );
            foreach (AFLOverUnderOutcome::cases() as $outcome) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'name' => $outcome->name(),
                    ],
                    ['result' => $outcome->value, 'sport' => LeagueSport::AFL]
                );
            }
        }
    }
}
