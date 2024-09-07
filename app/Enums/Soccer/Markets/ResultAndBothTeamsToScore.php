<?php

namespace App\Enums\Soccer\Markets;

use App\Contracts\BetMarket;
use App\Enums\MarketCategory;

use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Soccer\Outcomes\MatchWinner;
use App\Enums\Soccer\Outcomes\ResultAndBothTeamsToScoreOutcome;
use App\Enums\Soccer\Outcomes\YesNo;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum ResultAndBothTeamsToScore: string implements BetMarket
{
    case FULL_TIME = 'full_time';
    case HALF_TIME = 'half_time';

    public function oddsId(): int
    {
        return match ($this) {
            self::FULL_TIME => 24,
            self::HALF_TIME => 52,
        };
    }

    public function outcomes(): array
    {
        return ResultAndBothTeamsToScoreOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::FULL_TIME => "Winner/Both Teams Score",
            self::HALF_TIME => "Halftime Winner/Both Teams Score",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = ResultAndBothTeamsToScoreOutcome::from($bet->result);
        $period = $this === self::FULL_TIME ? 'fulltime' : 'halftime';

        $homeGoals = $game->getScores($period, GoalCount::HOME);
        $awayGoals = $game->getScores($period, GoalCount::AWAY);

        $result = match (true) {
            $homeGoals > $awayGoals => MatchWinner::HOME,
            $awayGoals > $homeGoals => MatchWinner::AWAY,
            default => MatchWinner::DRAW,
        };

        $bothTeamsScored = $homeGoals > 0 && $awayGoals > 0;

        return $outcome->matchWinner() === $result && $outcome->bothTeamsToScore() === ($bothTeamsScored ? YesNo::YES : YesNo::NO);
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' => EnumsMarket::RESULT_AND_BOTH_TEAMS_TO_SCORE,
                    'sport' => LeagueSport::FOOTBALL,
                ],
                [
                    'slug' => Str::slug($case->name()),
                    'description' => $case->name(),
                    'category' => MarketCategory::getCategory(self::class),
                    'name' => self::formatMarketName($case->name()),
                    'sport' => LeagueSport::FOOTBALL
                ]
            );
            foreach (ResultAndBothTeamsToScoreOutcome::cases() as $outcome) {
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
        return Str::of($name)->replace(['Home', 'Away'], ['{home}', '{away}']);
    }
}
