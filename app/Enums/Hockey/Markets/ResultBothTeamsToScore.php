<?php

namespace App\Enums\Hockey\Markets;

use App\Enums\MarketCategory;
use App\Contracts\BetMarket;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Hockey\Outcomes\ResultBothTeamsToScoreOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum ResultBothTeamsToScore: string implements BetMarket
{
    case FULL_TIME = 'total';
    case FIRST_PERIOD = 'first';
    case SECOND_PERIOD = 'second';
    case THIRD_PERIOD = 'third';

    public function oddsId(): int
    {
        return match ($this) {
            self::FULL_TIME => 5,
            self::FIRST_PERIOD => 23,
            self::SECOND_PERIOD => 15,
            self::THIRD_PERIOD => 24,
        };
    }

    public function outcomes(): array
    {
        return ResultBothTeamsToScoreOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::FULL_TIME => "Both Teams To Score",
            self::FIRST_PERIOD => "Both Teams To Score (1st Period)",
            self::SECOND_PERIOD => "Both Teams To Score (2nd Period)",
            self::THIRD_PERIOD => "Both Teams To Score (3rd Period)",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = ResultBothTeamsToScoreOutcome::from($bet->result);
        $homeScore = $game->getScores($this->value, GoalCount::HOME);
        $awayScore = $game->getScores($this->value, GoalCount::AWAY);

        $result = match (true) {
            $homeScore > $awayScore => 'home',
            $awayScore > $homeScore => 'away',
            default => 'draw',
        };

        $bothTeamsScored = $homeScore > 0 && $awayScore > 0;

        return $outcome->result() == $result && $outcome->bothTeamsScored() == $bothTeamsScored;
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' => EnumsMarket::HOCKEY_RESULT_BOTH_TEAMS_TO_SCORE,
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

            foreach (ResultBothTeamsToScoreOutcome::cases() as $outcome) {
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
