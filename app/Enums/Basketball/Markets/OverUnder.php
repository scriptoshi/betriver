<?php

namespace App\Enums\Basketball\Markets;

use App\Contracts\BetMarket;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Basketball\Outcomes\OverUnderOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;
use App\Enums\MarketCategory;

enum OverUnder: string implements BetMarket
{
    case FULL_TIME = 'full_time';
    case FIRST_HALF = 'first_half';
    case FIRST_QUARTER = 'first_quarter';

    public function oddsId(): int
    {
        return match ($this) {
            self::FULL_TIME => 4,
            self::FIRST_HALF => 5,
            self::FIRST_QUARTER => 16,
        };
    }

    public function outcomes(): array
    {
        return OverUnderOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::FULL_TIME => "Over/Under",
            self::FIRST_HALF => "Over/Under 1st Half",
            self::FIRST_QUARTER => "Over/Under 1st Qtr",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = OverUnderOutcome::from($bet->result);
        $totalScore = $this->getTotalScore($game);

        return match ($outcome->type()) {
            'over' => $totalScore > $outcome->threshold(),
            'under' => $totalScore < $outcome->threshold(),
        };
    }

    private function getTotalScore(Game $game): int
    {
        $homeScore = $this->getRelevantScore($game, 'home');
        $awayScore = $this->getRelevantScore($game, 'away');
        return $homeScore + $awayScore;
    }

    private function getRelevantScore(Game $game, string $team): int
    {
        return match ($this) {
            self::FULL_TIME => $game->getScores('total', $team),
            self::FIRST_HALF => $game->getScores('quarter_1', $team) + $game->getScores('quarter_2', $team),
            self::FIRST_QUARTER => $game->getScores('quarter_1', $team),
        };
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'sport' => LeagueSport::BASKETBALL
                ],
                [
                    'slug' => Str::slug(LeagueSport::BASKETBALL->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'category' => MarketCategory::getCategory(self::class),
                    'name' => self::formatMarketName($case->name()),
                    'type' => EnumsMarket::BASKETBALL_OVER_UNDER,
                    'sport' => LeagueSport::BASKETBALL
                ]
            );
            foreach (OverUnderOutcome::cases() as $outcome) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'result' => $outcome->value,
                    ],
                    ['name' => $outcome->name(), 'sport' => LeagueSport::BASKETBALL]
                );
            }
        }
    }

    private static function formatMarketName(string $name): string
    {
        return $name;
    }
}
