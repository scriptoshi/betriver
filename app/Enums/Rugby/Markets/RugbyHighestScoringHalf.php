<?php

namespace App\Enums\Rugby\Markets;

use App\Contracts\BetMarket;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Rugby\Outcomes\RugbyHighestScoringHalfOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum RugbyHighestScoringHalf: string implements BetMarket
{
    case FULL_GAME = 'full_game';

    public function oddsId(): int
    {
        return 7;
    }

    public function outcomes(): array
    {
        return RugbyHighestScoringHalfOutcome::cases();
    }

    public function name(): string
    {
        return "Highest Scoring Half";
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = RugbyHighestScoringHalfOutcome::from($bet->result);

        $firstHalfScore = $this->getHalfScore($game, 'halftime');
        $secondHalfScore = $this->getHalfScore($game, 'secondhalf');

        return match ($outcome) {
            RugbyHighestScoringHalfOutcome::FIRST_HALF => $firstHalfScore > $secondHalfScore,
            RugbyHighestScoringHalfOutcome::SECOND_HALF => $secondHalfScore > $firstHalfScore,
            RugbyHighestScoringHalfOutcome::EQUAL => $firstHalfScore === $secondHalfScore,
        };
    }

    private function getHalfScore(Game $game, string $period): int
    {
        return $game->getScores($period, GoalCount::HOME) + $game->getScores($period, GoalCount::AWAY);
    }

    public static function seed(): void
    {
        $market = Market::updateOrCreate(
            [
                'segment' => self::FULL_GAME->value,
                'oddsId' => self::FULL_GAME->oddsId(),
                'sport' => LeagueSport::RUGBY
            ],
            [
                'slug' => Str::slug(LeagueSport::RUGBY->value . '-' . self::FULL_GAME->name()),
                'description' => self::FULL_GAME->name(),
                'name' => self::FULL_GAME->name(),
                'type' => EnumsMarket::RUGBY_HIGHEST_SCORING_HALF,
                'sport' => LeagueSport::RUGBY
            ]
        );
        foreach (RugbyHighestScoringHalfOutcome::cases() as $outcome) {
            Bet::updateOrCreate(
                [
                    'market_id' => $market->id,
                    'name' => $outcome->name(),
                ],
                ['result' => $outcome->value, 'sport' => LeagueSport::RUGBY]
            );
        }
    }
}
