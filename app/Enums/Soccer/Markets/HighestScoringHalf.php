<?php

namespace App\Enums\Soccer\Markets;

use App\Contracts\BetMarket;

use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Soccer\Outcomes\HighestScoringHalfOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum HighestScoringHalf: string implements BetMarket
{
    case TOTAL = 'total';
    case HOME = 'home';
    case AWAY = 'away';

    public function oddsId(): int
    {
        return match ($this) {
            self::TOTAL => 11,
            self::HOME => 192,
            self::AWAY => 193,
        };
    }

    public function outcomes(): array
    {
        return HighestScoringHalfOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::TOTAL => "Highest Scoring Half",
            self::HOME => "Home Highest Scoring Half",
            self::AWAY => "Away Highest Scoring Half",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = HighestScoringHalfOutcome::from($bet->result);
        $firstHalfGoals = $this->getGoals($game, 'halftime');
        $secondHalfGoals = $this->getGoals($game, 'secondhalf');

        return match ($outcome) {
            HighestScoringHalfOutcome::FIRST => $firstHalfGoals > $secondHalfGoals,
            HighestScoringHalfOutcome::SECOND => $secondHalfGoals > $firstHalfGoals,
            HighestScoringHalfOutcome::EQUAL => $firstHalfGoals === $secondHalfGoals,
        };
    }

    private function getGoals(Game $game, string $half): int
    {
        if ($this === self::TOTAL) {
            return $game->getScores($half, GoalCount::HOME) + $game->getScores($half, GoalCount::AWAY);
        } elseif ($this === self::HOME) {
            return $game->getScores($half, GoalCount::HOME);
        } else { // AWAY
            return $game->getScores($half, GoalCount::AWAY);
        }
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' => EnumsMarket::HIGHEST_SCORING_HALF,
                    'sport' => LeagueSport::FOOTBALL,
                ],
                [
                    'slug' => Str::slug($case->name()),
                    'description' => $case->name(),
                    'name' => self::formatMarketName($case->name()),
                    'sport' => LeagueSport::FOOTBALL
                ]
            );

            foreach (HighestScoringHalfOutcome::cases() as $outcome) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'name' => $outcome->name(),
                    ],
                    ['result' => $outcome->value, 'sport' => LeagueSport::FOOTBALL]
                );
            }
        }
    }

    private static function formatMarketName(string $name): string
    {
        return Str::of($name)->replace(['Home', 'Away'], ['{home}', '{away}']);
    }
}
