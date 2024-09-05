<?php

namespace App\Enums\Nfl\Markets;

use App\Contracts\BetMarket;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Afl\Outcomes\AFLAsianHandicapOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum NFLAsianHandicap: string implements BetMarket
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
            self::FULL_TIME => 2,
            self::FIRST_HALF => 11,
            self::SECOND_HALF => 36,
            self::FIRST_QUARTER => 21,
            self::SECOND_QUARTER => 45,
            self::THIRD_QUARTER => 46,
            self::FOURTH_QUARTER => 32,
        };
    }

    public function outcomes(): array
    {
        return AFLAsianHandicapOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::FULL_TIME => "Asian Handicap",
            self::FIRST_HALF => "Asian Handicap First Half",
            self::SECOND_HALF => "Asian Handicap (2nd Half)",
            self::FIRST_QUARTER => "Asian Handicap 1st Qtr",
            self::SECOND_QUARTER => "Asian Handicap 2nd Qtr",
            self::THIRD_QUARTER => "Asian Handicap 3rd Qtr",
            self::FOURTH_QUARTER => "Asian Handicap 4th Qtr",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = AFLAsianHandicapOutcome::from($bet->result);
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

        $handicapValue = $outcome->handicapValue();
        $adjustedHomeScore = $homeScore + $handicapValue;

        $scoreDifference = $adjustedHomeScore - $awayScore;

        return match ($outcome->team()) {
            'home' => $scoreDifference > 0,
            'away' => $scoreDifference < 0,
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
                    'name' => self::formatMarketName($case->name()),
                    'type' => EnumsMarket::NFL_ASIAN_HANDICAP,
                    'sport' => LeagueSport::NFL
                ]
            );
            foreach (AFLAsianHandicapOutcome::cases() as $outcome) {
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

    private static function formatMarketName(string $name): string
    {
        return Str::of($name)->replace(['Home', 'Away'], ['{home}', '{away}']);
    }
}
