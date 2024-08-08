<?php

namespace App\Enums\Handball\Markets;

use App\Contracts\BetMarket;
use App\Enums\Handball\Outcomes\ResultBothTeamsToScoreOutcome;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum ResultBothTeamsToScore: string implements BetMarket
{
    case FULL_TIME = 'total';

    public function oddsId(): int
    {
        return 59;
    }

    public function outcomes(): array
    {
        return ResultBothTeamsToScoreOutcome::cases();
    }

    public function name(): string
    {
        return "Results/Both Teams To Score";
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = ResultBothTeamsToScoreOutcome::from($bet->result);

        $homeScore = $game->getScores($this->value, 'home');
        $awayScore = $game->getScores($this->value, 'away');

        $result = match (true) {
            $homeScore > $awayScore => 'home',
            $awayScore > $homeScore => 'away',
            default => 'draw',
        };

        $bothTeamsScored = $homeScore > 0 && $awayScore > 0;

        return $outcome->result() === $result && $outcome->bothTeamsScore() === $bothTeamsScored;
    }

    public static function seed(): void
    {
        $market = Market::updateOrCreate(
            [
                'segment' => self::FULL_TIME->value,
                'oddsId' => self::FULL_TIME->oddsId(),
                'type' => EnumsMarket::HANDBALL_RESULT_BOTH_TEAMS_TO_SCORE,
                'sport' => LeagueSport::HANDBALL,
            ],
            [
                'slug' => Str::slug(self::FULL_TIME->name()),
                'description' => self::FULL_TIME->name(),
                'name' => self::FULL_TIME->name(),
            ]
        );

        foreach (ResultBothTeamsToScoreOutcome::cases() as $outcome) {
            Bet::updateOrCreate(
                [
                    'market_id' => $market->id,
                    'name' => $outcome->name(),
                ],
                [
                    'result' => $outcome->value,
                    'sport' => LeagueSport::HANDBALL,
                ]
            );
        }
    }
}
