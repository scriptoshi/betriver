<?php

namespace App\Enums\Soccer\Markets;

use App\Contracts\BetMarket;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Soccer\Outcomes\ExactGoalsOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum ExactGoals: string  implements BetMarket
{
    case FULLTIME_TOTAL = 'fulltime_total';
    case FIRSTHALF_TOTAL = 'firsthalf_total';
    case SECONDHALF_TOTAL = 'secondhalf_total';
    case FULLTIME_HOME = 'fulltime_home';
    case FULLTIME_AWAY = 'fulltime_away';
    case FIRSTHALF_HOME = 'firsthalf_home';
    case FIRSTHALF_AWAY = 'firsthalf_away';

    public function oddsId(): int
    {
        return match ($this) {
            self::FULLTIME_TOTAL => 38,
            self::FIRSTHALF_TOTAL => 46,
            self::SECONDHALF_TOTAL => 42,
            self::FULLTIME_HOME => 40,
            self::FULLTIME_AWAY => 41,
            self::FIRSTHALF_HOME => 190,
            self::FIRSTHALF_AWAY => 191,
        };
    }

    public function outcomes(): array
    {
        return match ($this) {
            self::FULLTIME_TOTAL => ExactGoalsOutcome::sixOrLess(),
            default => ExactGoalsOutcome::threeOrLess()
        };
    }

    public function name(): string
    {
        return match ($this) {
            self::FULLTIME_TOTAL => "Exact Goals Number",
            self::FIRSTHALF_TOTAL => "Exact Goals Number - First Half",
            self::SECONDHALF_TOTAL => "Second Half Exact Goals Number",
            self::FULLTIME_HOME => "Home Exact Goals Number",
            self::FULLTIME_AWAY => "Away Exact Goals Number",
            self::FIRSTHALF_HOME => "Home Exact Goals Number (1st Half)",
            self::FIRSTHALF_AWAY => "Away Exact Goals Number (1st Half)",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = ExactGoalsOutcome::from($bet->result);
        $actualGoals = $this->calculateGoals($game);
        if ($outcome == ExactGoalsOutcome::SIX_PLUS)
            return $actualGoals >= 6;
        if ($outcome == ExactGoalsOutcome::THREE_PLUS)
            return $actualGoals >= 3;
        return $actualGoals == (int) $outcome->value;
    }

    private function calculateGoals(Game $game): int
    {
        $period = match ($this) {
            self::FULLTIME_TOTAL, self::FULLTIME_HOME, self::FULLTIME_AWAY => 'fulltime',
            self::FIRSTHALF_TOTAL, self::FIRSTHALF_HOME, self::FIRSTHALF_AWAY => 'halftime',
            self::SECONDHALF_TOTAL => 'secondhalf',
        };

        $goalCount = match ($this) {
            self::FULLTIME_TOTAL, self::FIRSTHALF_TOTAL, self::SECONDHALF_TOTAL => GoalCount::TOTAL,
            self::FULLTIME_HOME, self::FIRSTHALF_HOME => GoalCount::HOME,
            self::FULLTIME_AWAY, self::FIRSTHALF_AWAY => GoalCount::AWAY,
        };

        if ($goalCount === GoalCount::TOTAL) {
            return $game->getScores($period, GoalCount::HOME) + $game->getScores($period, GoalCount::AWAY);
        }

        return $game->getScores($period, $goalCount);
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' => EnumsMarket::EXACT_GOALS,
                    'sport' => LeagueSport::FOOTBALL
                ],
                [
                    'slug' => Str::slug($case->name()),
                    'description' => $case->name(),
                    'name' => self::formatMarketName($case->name()),
                    'sport' => LeagueSport::FOOTBALL

                ]
            );

            foreach ($case->outcomes() as $outcome) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'result' => $outcome->value,
                    ],
                    ['name' => $outcome->name(), 'sport' => LeagueSport::FOOTBALL]
                );
            }
        }
    }

    private static function formatMarketName(string $name): string
    {
        return Str::of($name)
            ->replace(['Home Team', 'Away Team', 'Home', 'Away'], ['{home}', '{away}', '{home}', '{away}']);
    }
}
