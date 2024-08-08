<?php

namespace App\Enums\Hockey\Markets;

use App\Contracts\BetMarket;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Hockey\Outcomes\OverUnderOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum OverUnder: string implements BetMarket
{
    case FULL_TIME = 'total';
    case FIRST_PERIOD = 'first';
    case SECOND_PERIOD = 'second';
    case THIRD_PERIOD = 'third';

    public function oddsId(): int
    {
        return match ($this) {
            self::FULL_TIME => 4,
            self::FIRST_PERIOD => 14,
            self::SECOND_PERIOD => 19,
            self::THIRD_PERIOD => 20,
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
            self::FIRST_PERIOD => "Over/Under (1st Period)",
            self::SECOND_PERIOD => "Over/Under (2nd Period)",
            self::THIRD_PERIOD => "Over/Under (3rd Period)",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = OverUnderOutcome::from($bet->result);
        $totalGoals = $game->getScores($this->value, GoalCount::HOME) + $game->getScores($this->value, GoalCount::AWAY);

        return match ($outcome->type()) {
            'over' => $totalGoals > $outcome->threshold(),
            'under' => $totalGoals < $outcome->threshold(),
        };
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' => EnumsMarket::HOCKEY_OVER_UNDER,
                    'sport' => LeagueSport::HOCKEY
                ],
                [
                    'slug' => Str::slug(LeagueSport::HOCKEY->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'name' => self::formatMarketName($case->name()),
                    'sport' => LeagueSport::HOCKEY
                ]
            );

            foreach (OverUnderOutcome::cases() as $outcome) {
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
        return Str::of($name)->replace(['Home', 'Away'], ['{home}', '{away}']);
    }
}
