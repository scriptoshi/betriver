<?php

namespace App\Enums\Rugby\Markets;

use App\Contracts\BetMarket;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Rugby\Outcomes\RugbyOddEvenOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum RugbyOddEven: string implements BetMarket
{
    case FULL_GAME = 'full_game';
    case FIRST_HALF = 'first_half';
    case SECOND_HALF = 'second_half';
    case HOME = 'home';
    case AWAY = 'away';

    public function oddsId(): int
    {
        return match ($this) {
            self::FULL_GAME => 11,
            self::FIRST_HALF => 39,
            self::SECOND_HALF => 40,
            self::HOME => 42,
            self::AWAY => 43,
        };
    }

    public function outcomes(): array
    {
        return RugbyOddEvenOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::FULL_GAME => "Odd/Even",
            self::FIRST_HALF => "Odd/Even 1st Half",
            self::SECOND_HALF => "Odd/Even (2nd Half)",
            self::HOME => "Home Odd/Even",
            self::AWAY => "Away Odd/Even",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = RugbyOddEvenOutcome::from($bet->result);
        $totalScore = $this->calculateTotalScore($game);
        $isOdd = $totalScore % 2 !== 0;

        return match ($outcome) {
            RugbyOddEvenOutcome::ODD => $isOdd,
            RugbyOddEvenOutcome::EVEN => !$isOdd,
        };
    }

    private function calculateTotalScore(Game $game): int
    {
        return match ($this) {
            self::FULL_GAME => $game->getScores('fulltime', GoalCount::HOME) + $game->getScores('fulltime', GoalCount::AWAY),
            self::FIRST_HALF => $game->getScores('halftime', GoalCount::HOME) + $game->getScores('halftime', GoalCount::AWAY),
            self::SECOND_HALF => $game->getScores('secondhalf', GoalCount::HOME) + $game->getScores('secondhalf', GoalCount::AWAY),
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
                    'sport' => LeagueSport::RUGBY
                ],
                [
                    'slug' => Str::slug(LeagueSport::RUGBY->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'name' => self::formatMarketName($case->name()),
                    'sport' => LeagueSport::RUGBY,
                    'type' => EnumsMarket::RUGBY_ODD_EVEN
                ]
            );
            foreach (RugbyOddEvenOutcome::cases() as $outcome) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'result' => $outcome->value,
                    ],
                    ['name' => $outcome->name(), 'sport' => LeagueSport::RUGBY]
                );
            }
        }
    }

    private static function formatMarketName(string $name): string
    {
        return Str::of($name)->replace(['Home', 'Away'], ['{home}', '{away}']);
    }
}
