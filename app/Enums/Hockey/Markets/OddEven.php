<?php

namespace App\Enums\Hockey\Markets;

use App\Contracts\BetMarket;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Hockey\Outcomes\OddEvenOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum OddEven: string implements BetMarket
{
    case FULL_TIME = 'total';
    case FIRST_PERIOD = 'first';
    case SECOND_PERIOD = 'second';
    case THIRD_PERIOD = 'third';

    public function oddsId(): int
    {
        return match ($this) {
            self::FULL_TIME => 13,
            self::FIRST_PERIOD => 43,
            self::SECOND_PERIOD => 44,
            self::THIRD_PERIOD => 45,
        };
    }

    public function outcomes(): array
    {
        return OddEvenOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::FULL_TIME => "Odd/Even",
            self::FIRST_PERIOD => "Odd/Even (1st Period)",
            self::SECOND_PERIOD => "Odd/Even (2nd Period)",
            self::THIRD_PERIOD => "Odd/Even (3rd Period)",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = OddEvenOutcome::from($bet->result);
        $totalGoals = $game->getScores($this->value, GoalCount::HOME) + $game->getScores($this->value, GoalCount::AWAY);

        return match ($outcome) {
            OddEvenOutcome::ODD => $totalGoals % 2 !== 0,
            OddEvenOutcome::EVEN => $totalGoals % 2 === 0,
        };
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' => EnumsMarket::HOCKEY_ODD_EVEN,
                    'sport' => LeagueSport::HOCKEY
                ],
                [
                    'slug' => Str::slug(LeagueSport::HOCKEY->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'name' => self::formatMarketName($case->name()),
                    'sport' => LeagueSport::HOCKEY
                ]
            );

            foreach (OddEvenOutcome::cases() as $outcome) {
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
        return $name;
    }
}
