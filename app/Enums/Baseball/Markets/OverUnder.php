<?php

namespace App\Enums\Baseball\Markets;

use App\Contracts\BetMarket;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Baseball\Outcomes\OverUnderOutcome;
use App\Enums\GoalCount;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum OverUnder: string implements BetMarket
{
    case FULL_GAME = 'full_game';
    case FIRST_INNING = 'innings_1';
    case FIRST_THREE_INNINGS = 'first_three_innings';
    case FIRST_FIVE_INNINGS = 'first_five_innings';
    case FIRST_SEVEN_INNINGS = 'first_seven_innings';
    case OVERTIME = 'overtime';

    public function oddsId(): int
    {
        return match ($this) {
            self::FULL_GAME => 5,
            self::FIRST_INNING => 11,
            self::FIRST_THREE_INNINGS => 31,
            self::FIRST_FIVE_INNINGS => 6,
            self::FIRST_SEVEN_INNINGS => 30,
            self::OVERTIME => 18,
        };
    }

    public function outcomes(): array
    {
        return OverUnderOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::FULL_GAME => "Over/Under",
            self::FIRST_INNING => "Over/Under (1st Inning)",
            self::FIRST_THREE_INNINGS => "Over/Under (1st 3 Innings)",
            self::FIRST_FIVE_INNINGS => "Over/Under (1st 5 Innings)",
            self::FIRST_SEVEN_INNINGS => "Over/Under (1st 7 Innings)",
            self::OVERTIME => "Over/Under (OT)",
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
        return match ($this) {
            self::FULL_GAME => $game->getScores('total', GoalCount::TOTAL),
            self::FIRST_INNING => $game->getScores('innings_1', GoalCount::TOTAL),
            self::FIRST_THREE_INNINGS => $game->getScores(['innings_1', 'innings_2', 'innings_3'], GoalCount::TOTAL),
            self::FIRST_FIVE_INNINGS => $game->getScores(['innings_1', 'innings_2', 'innings_3', 'innings_4', 'innings_5'], GoalCount::TOTAL),
            self::FIRST_SEVEN_INNINGS => $game->getScores(['innings_1', 'innings_2', 'innings_3', 'innings_4', 'innings_5', 'innings_6', 'innings_7'], GoalCount::TOTAL),
            self::OVERTIME => $game->getScores('extra', GoalCount::TOTAL),
        };
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' => EnumsMarket::BASEBALL_OVER_UNDER,
                    'sport' => LeagueSport::BASEBALL,
                ],
                [
                    'slug' => Str::slug(LeagueSport::BASEBALL->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'name' => self::formatMarketName($case->name()),
                    'sport' => LeagueSport::BASEBALL,
                ]
            );

            foreach (OverUnderOutcome::cases() as $outcome) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'name' => $outcome->name(),
                    ],
                    ['result' => $outcome->value, 'sport' => LeagueSport::BASEBALL]
                );
            }
        }
    }

    private static function formatMarketName(string $name): string
    {
        return $name;
    }
}
