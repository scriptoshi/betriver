<?php

namespace App\Enums\Handball\Markets;

use App\Enums\MarketCategory;
use App\Contracts\BetMarket;
use App\Enums\Handball\Outcomes\HighestScoringHalfOutcome;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum HighestScoringHalf: string implements BetMarket
{
    case HIGHEST_SCORING_HALF = 'highest_scoring_half';

    public function oddsId(): int
    {
        return 10;
    }

    public function outcomes(): array
    {
        return HighestScoringHalfOutcome::cases();
    }

    public function name(): string
    {
        return "Highest Scoring Half";
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = HighestScoringHalfOutcome::from($bet->result);

        $firstHalfGoals = $game->getScores('firsthalf', 'home') + $game->getScores('firsthalf', 'away');
        $secondHalfGoals = $game->getScores('secondhalf', 'home') + $game->getScores('secondhalf', 'away');

        return match ($outcome) {
            HighestScoringHalfOutcome::FIRST_HALF => $firstHalfGoals > $secondHalfGoals,
            HighestScoringHalfOutcome::SECOND_HALF => $secondHalfGoals > $firstHalfGoals,
            HighestScoringHalfOutcome::EQUAL => $firstHalfGoals === $secondHalfGoals,
        };
    }

    public static function seed(): void
    {
        $market = Market::updateOrCreate(
            [
                'segment' => self::HIGHEST_SCORING_HALF->value,
                'oddsId' => self::HIGHEST_SCORING_HALF->oddsId(),
                'type' => EnumsMarket::HANDBALL_HIGHEST_SCORING_HALF,
                'sport' => LeagueSport::HANDBALL,
            ],
            [
                'slug' => Str::slug(LeagueSport::HANDBALL->value . '-' . self::HIGHEST_SCORING_HALF->name()),
                'description' => self::HIGHEST_SCORING_HALF->name(),
                'name' => self::HIGHEST_SCORING_HALF->name(),
                'category' => MarketCategory::getCategory(self::class),
            ]
        );

        foreach (HighestScoringHalfOutcome::cases() as $outcome) {
            Bet::updateOrCreate(
                [
                    'market_id' => $market->id,
                    'result' => $outcome->value,
                ],
                [
                    'name' => $outcome->name(),
                    'sport' => LeagueSport::HANDBALL,
                ]
            );
        }
    }
}
