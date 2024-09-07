<?php

namespace App\Enums\Basketball\Markets;

use App\Contracts\BetMarket;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Basketball\Outcomes\OddEvenOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;
use App\Enums\MarketCategory;

enum OddEven: string implements BetMarket
{
    case FULL_TIME = 'full_time';
    case FIRST_HALF = 'first_half';

    public function oddsId(): int
    {
        return match ($this) {
            self::FULL_TIME => 12,
            self::FIRST_HALF => 13,
        };
    }

    public function outcomes(): array
    {
        return OddEvenOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::FULL_TIME => "Odd/Even (Including OT)",
            self::FIRST_HALF => "Odd/Even 1st Half",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = OddEvenOutcome::from($bet->result);
        $totalScore = $this->getTotalScore($game);
        $isEven = $totalScore % 2 === 0;

        return match ($outcome) {
            OddEvenOutcome::ODD => !$isEven,
            OddEvenOutcome::EVEN => $isEven,
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
            self::FULL_TIME => $game->getScores(['total', 'over_time'], $team),
            self::FIRST_HALF => $game->getScores(['quarter_1', 'quarter_2'],  $team),
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
                    'type' => EnumsMarket::BASKETBALL_ODD_EVEN,
                    'sport' => LeagueSport::BASKETBALL
                ]
            );
            foreach (OddEvenOutcome::cases() as $outcome) {
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
