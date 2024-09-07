<?php

namespace App\Enums\Hockey\Markets;

use App\Enums\MarketCategory;
use App\Contracts\BetMarket;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Hockey\Outcomes\TeamGoalsOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum TeamGoals: string implements BetMarket
{
    case HOME_FULL_TIME = 'home_total';
    case AWAY_FULL_TIME = 'away_total';
    case HOME_FIRST_PERIOD = 'home_first';
    case AWAY_FIRST_PERIOD = 'away_first';
    case HOME_SECOND_PERIOD = 'home_second';
    case AWAY_SECOND_PERIOD = 'away_second';
    case HOME_THIRD_PERIOD = 'home_third';
    case AWAY_THIRD_PERIOD = 'away_third';

    public function oddsId(): int
    {
        return match ($this) {
            self::HOME_FULL_TIME => 10,
            self::AWAY_FULL_TIME => 11,
            self::HOME_FIRST_PERIOD => 29,
            self::AWAY_FIRST_PERIOD => 32,
            self::HOME_SECOND_PERIOD => 30,
            self::AWAY_SECOND_PERIOD => 33,
            self::HOME_THIRD_PERIOD => 31,
            self::AWAY_THIRD_PERIOD => 34,
        };
    }

    public function outcomes(): array
    {
        return TeamGoalsOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::HOME_FULL_TIME => "Total - Home",
            self::AWAY_FULL_TIME => "Total - Away",
            self::HOME_FIRST_PERIOD => "Home Team Total Goals (1st Period)",
            self::AWAY_FIRST_PERIOD => "Away Team Total Goals (1st Period)",
            self::HOME_SECOND_PERIOD => "Home Team Total Goals (2nd Period)",
            self::AWAY_SECOND_PERIOD => "Away Team Total Goals (2nd Period)",
            self::HOME_THIRD_PERIOD => "Home Team Total Goals (3rd Period)",
            self::AWAY_THIRD_PERIOD => "Away Team Total Goals (3rd Period)",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = TeamGoalsOutcome::from($bet->result);
        $team = Str::before($this->value, '_');
        $period = Str::after($this->value, '_');
        $goals = $game->getScores($period, $team === 'home' ? GoalCount::HOME : GoalCount::AWAY);

        return $outcome->isCorrect($goals);
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' => EnumsMarket::HOCKEY_TEAM_GOALS,
                    'sport' => LeagueSport::HOCKEY
                ],
                [
                    'slug' => Str::slug(LeagueSport::HOCKEY->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'category' => MarketCategory::getCategory(self::class),
                    'name' => self::formatMarketName($case->name()),
                    'sport' => LeagueSport::HOCKEY
                ]
            );

            foreach (TeamGoalsOutcome::cases() as $outcome) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'result' => $outcome->value,
                    ],
                    [
                        'name' => $outcome->name(),
                        'sport' => LeagueSport::HOCKEY
                    ]
                );
            }
        }
    }

    private static function formatMarketName(string $name): string
    {
        return Str::of($name)->replace(['Home', 'Away'], ['{home}', '{away}']);
    }
}
