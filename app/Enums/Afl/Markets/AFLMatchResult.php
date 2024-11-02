<?php

namespace App\Enums\Afl\Markets;

use App\Contracts\BetMarket;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Afl\Outcomes\AFLMatchResultOutcome;
use App\Enums\Afl\ScoreType;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;
use App\Enums\MarketCategory;

enum AFLMatchResult: string implements BetMarket
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
            self::FULL_TIME => 1,
            self::FIRST_HALF => 8,
            self::SECOND_HALF => 2,
            self::FIRST_QUARTER => 12,
            self::SECOND_QUARTER => 13,
            self::THIRD_QUARTER => 14,
            self::FOURTH_QUARTER => 15,
        };
    }

    public function outcomes(): array
    {
        return AFLMatchResultOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::FULL_TIME => "Home/Away",
            self::FIRST_HALF => "1st Half 3Way Result",
            self::SECOND_HALF => "2nd Half 3Way Result",
            self::FIRST_QUARTER => "3Way Result - 1st Qtr",
            self::SECOND_QUARTER => "3Way Result - 2nd Qtr",
            self::THIRD_QUARTER => "3Way Result - 3rd Qtr",
            self::FOURTH_QUARTER => "3Way Result - 4th Qtr",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = AFLMatchResultOutcome::from($bet->result);
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

        return match ($outcome) {
            AFLMatchResultOutcome::HOME => $homeScore > $awayScore,
            AFLMatchResultOutcome::AWAY => $awayScore > $homeScore,
            AFLMatchResultOutcome::DRAW => $homeScore == $awayScore,
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
                    'name' => self::formatMarketName($case->name()),
                    'is_default' => $case == self::FULL_TIME,
                    'sport' => LeagueSport::AFL,
                    'type' => EnumsMarket::AFL_MATCH_RESULT, //'AFLMatchResult',
                    'category' => MarketCategory::getCategory(self::class),
                ]
            );
            foreach (AFLMatchResultOutcome::cases() as $outcome) {
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

    private static function formatMarketName(string $name): string
    {
        return Str::of($name)->replace(['Home', 'Away'], ['{home}', '{away}']);
    }
}
