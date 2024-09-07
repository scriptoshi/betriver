<?php

namespace App\Enums\Nfl\Markets;

use App\Contracts\BetMarket;
use App\Enums\MarketCategory;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Afl\Outcomes\AFLOverUnderOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum NFLOverUnder: string implements BetMarket
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
            self::FULL_TIME => 3,
            self::FIRST_HALF => 4,
            self::SECOND_HALF => 30,
            self::FIRST_QUARTER => 20,
            self::SECOND_QUARTER => 33,
            self::THIRD_QUARTER => 34,
            self::FOURTH_QUARTER => 35,
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
            self::FULL_TIME => 'fulltime',
            self::FIRST_HALF => 'halftime',
            self::SECOND_HALF => 'secondhalf',
            self::FIRST_QUARTER => 'firstquarter',
            self::SECOND_QUARTER => 'secondquarter',
            self::THIRD_QUARTER => 'thirdquarter',
            self::FOURTH_QUARTER => 'fourthquarter',
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
                    'sport' => LeagueSport::NFL
                ],
                [
                    'slug' => Str::slug(LeagueSport::NFL->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'category' => MarketCategory::getCategory(self::class),
                    'name' => $case->name(),
                    'sport' => LeagueSport::NFL,
                    'type' => EnumsMarket::NFL_OVER_UNDER
                ]
            );
            foreach (AFLOverUnderOutcome::cases() as $outcome) {
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
