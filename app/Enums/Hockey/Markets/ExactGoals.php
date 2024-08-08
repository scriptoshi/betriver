<?php

namespace App\Enums\Hockey\Markets;

use App\Contracts\BetMarket;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Hockey\Outcomes\ExactGoalsOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum ExactGoals: string implements BetMarket
{
    case FULL_TIME = 'total';
    case HOME_TEAM = 'home';
    case AWAY_TEAM = 'away';

    public function oddsId(): int
    {
        return match ($this) {
            self::FULL_TIME => 141,
            self::HOME_TEAM => 142,
            self::AWAY_TEAM => 143,
        };
    }

    public function outcomes(): array
    {
        return ExactGoalsOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::FULL_TIME => "Exact Goals Number",
            self::HOME_TEAM => "Home Team Exact Goals Number",
            self::AWAY_TEAM => "Away Team Exact Goals Number",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = ExactGoalsOutcome::from($bet->result);
        $goals = match ($this) {
            self::FULL_TIME => $game->getScores($this->value, GoalCount::HOME) + $game->getScores($this->value, GoalCount::AWAY),
            self::HOME_TEAM => $game->getScores($this->value, GoalCount::HOME),
            self::AWAY_TEAM => $game->getScores($this->value, GoalCount::AWAY),
        };

        return $outcome->goals() === $goals;
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' => EnumsMarket::HOCKEY_EXACT_GOALS,
                    'sport' => LeagueSport::HOCKEY
                ],
                [
                    'slug' => Str::slug(LeagueSport::HOCKEY->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'name' => self::formatMarketName($case->name()),
                    'sport' => LeagueSport::HOCKEY
                ]
            );

            foreach (ExactGoalsOutcome::cases() as $outcome) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'name' => $outcome->name(),
                    ],
                    [
                        'result' => $outcome->value,
                        'sport' => LeagueSport::HOCKEY
                    ]
                );
            }
        }
    }

    private static function formatMarketName(string $name): string
    {
        return Str::of($name)->replace(['Home Team', 'Away Team'], ['{home}', '{away}']);
    }
}
