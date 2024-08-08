<?php

namespace App\Enums\Hockey\Markets;

use App\Contracts\BetMarket;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Soccer\Outcomes\YesNo;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum TeamToScore: string implements BetMarket
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
            self::HOME_FULL_TIME => 59,
            self::AWAY_FULL_TIME => 60,
            self::HOME_FIRST_PERIOD => 61,
            self::AWAY_FIRST_PERIOD => 64,
            self::HOME_SECOND_PERIOD => 62,
            self::AWAY_SECOND_PERIOD => 65,
            self::HOME_THIRD_PERIOD => 63,
            self::AWAY_THIRD_PERIOD => 66,
        };
    }

    public function outcomes(): array
    {
        return YesNo::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::HOME_FULL_TIME => "Home team will score a goal",
            self::AWAY_FULL_TIME => "Away team will score a goal",
            self::HOME_FIRST_PERIOD => "Home team will score a goal (1st Period)",
            self::AWAY_FIRST_PERIOD => "Away team will score a goal (1st Period)",
            self::HOME_SECOND_PERIOD => "Home team will score a goal (2nd Period)",
            self::AWAY_SECOND_PERIOD => "Away team will score a goal (2nd Period)",
            self::HOME_THIRD_PERIOD => "Home team will score a goal (3rd Period)",
            self::AWAY_THIRD_PERIOD => "Away team will score a goal (3rd Period)",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = YesNo::from($bet->result);
        $team = Str::before($this->value, '_');
        $period = Str::after($this->value, '_');
        $goals = $game->getScores($period, $team === 'home' ? GoalCount::HOME : GoalCount::AWAY);

        return match ($outcome) {
            YesNo::YES => $goals > 0,
            YesNo::NO => $goals === 0,
        };
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' => EnumsMarket::HOCKEY_TEAM_TO_SCORE,
                    'sport' => LeagueSport::HOCKEY
                ],
                [
                    'slug' => Str::slug(LeagueSport::HOCKEY->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'name' => self::formatMarketName($case->name()),
                    'sport' => LeagueSport::HOCKEY
                ]
            );

            foreach (YesNo::cases() as $outcome) {
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
        return Str::of($name)->replace(['Home team', 'Away team'], ['{home}', '{away}']);
    }
}
