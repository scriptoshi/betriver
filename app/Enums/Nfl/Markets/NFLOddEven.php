<?php

namespace App\Enums\Nfl\Markets;

use App\Contracts\BetMarket;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Afl\Outcomes\AFLOddEvenOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum NFLOddEven: string implements BetMarket
{
    case FULL_GAME = 'full_game';
    case FIRST_HALF = 'first_half';
    case SECOND_HALF = 'second_half';
    case FIRST_QUARTER = 'first_quarter';
    case SECOND_QUARTER = 'second_quarter';
    case THIRD_QUARTER = 'third_quarter';
    case FOURTH_QUARTER = 'fourth_quarter';
    case HOME = 'home';
    case AWAY = 'away';

    public function oddsId(): int
    {
        return match ($this) {
            self::FULL_GAME => 13,
            self::FIRST_HALF => 14,
            self::SECOND_HALF => 42,
            self::FIRST_QUARTER => 28,
            self::SECOND_QUARTER => 43,
            self::THIRD_QUARTER => 44,
            self::FOURTH_QUARTER => 62,
            self::HOME => 15,
            self::AWAY => 16,
        };
    }

    public function outcomes(): array
    {
        return AFLOddEvenOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::FULL_GAME => "Odd/Even",
            self::FIRST_HALF => "Odd/Even 1st Half",
            self::SECOND_HALF => "Odd/Even (2nd Half)",
            self::FIRST_QUARTER => "Odd/Even (1st Quarter)",
            self::SECOND_QUARTER => "Odd/Even (2nd Quarter)",
            self::THIRD_QUARTER => "Odd/Even (3rd Quarter)",
            self::FOURTH_QUARTER => "Odd/Even (4th Quarter)",
            self::HOME => "Home Odd/Even",
            self::AWAY => "Away Odd/Even",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = AFLOddEvenOutcome::from($bet->result);
        $totalScore = $this->calculateTotalScore($game);
        $isOdd = $totalScore % 2 !== 0;

        return match ($outcome) {
            AFLOddEvenOutcome::ODD => $isOdd,
            AFLOddEvenOutcome::EVEN => !$isOdd,
        };
    }

    private function calculateTotalScore(Game $game): int
    {
        return match ($this) {
            self::FULL_GAME => $game->getScores('fulltime', GoalCount::HOME) + $game->getScores('fulltime', GoalCount::AWAY),
            self::FIRST_HALF => $game->getScores('halftime', GoalCount::HOME) + $game->getScores('halftime', GoalCount::AWAY),
            self::SECOND_HALF => $game->getScores('secondhalf', GoalCount::HOME) + $game->getScores('secondhalf', GoalCount::AWAY),
            self::FIRST_QUARTER => $game->getScores('firstquarter', GoalCount::HOME) + $game->getScores('firstquarter', GoalCount::AWAY),
            self::SECOND_QUARTER => $game->getScores('secondquarter', GoalCount::HOME) + $game->getScores('secondquarter', GoalCount::AWAY),
            self::THIRD_QUARTER => $game->getScores('thirdquarter', GoalCount::HOME) + $game->getScores('thirdquarter', GoalCount::AWAY),
            self::FOURTH_QUARTER => $game->getScores('fourthquarter', GoalCount::HOME) + $game->getScores('fourthquarter', GoalCount::AWAY),
            self::HOME => $game->getScores('fulltime', GoalCount::HOME),
            self::AWAY => $game->getScores('fulltime', GoalCount::AWAY),
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
                    'sport' => LeagueSport::NFL,
                    'type' => EnumsMarket::NFL_ODD_EVEN
                ]
            );
            foreach (AFLOddEvenOutcome::cases() as $outcome) {
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
