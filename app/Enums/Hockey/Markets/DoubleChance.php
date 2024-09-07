<?php

namespace App\Enums\Hockey\Markets;

use App\Enums\MarketCategory;
use App\Contracts\BetMarket;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Hockey\Outcomes\DoubleChanceOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum DoubleChance: string implements BetMarket
{
    case FULL_TIME = 'total';
    case FIRST_PERIOD = 'first';
    case SECOND_PERIOD = 'second';
    case THIRD_PERIOD = 'third';

    public function oddsId(): int
    {
        return match ($this) {
            self::FULL_TIME => 9,
            self::FIRST_PERIOD => 27,
            self::SECOND_PERIOD => 28,
            self::THIRD_PERIOD => 22,
        };
    }

    public function outcomes(): array
    {
        return DoubleChanceOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::FULL_TIME => "Double Chance",
            self::FIRST_PERIOD => "Double Chance (1st Period)",
            self::SECOND_PERIOD => "Double Chance (2nd Period)",
            self::THIRD_PERIOD => "Double Chance (3rd Period)",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = DoubleChanceOutcome::from($bet->result);
        $homeScore = $game->getScores($this->value, GoalCount::HOME);
        $awayScore = $game->getScores($this->value, GoalCount::AWAY);

        return match ($outcome) {
            DoubleChanceOutcome::HOME_OR_DRAW => $homeScore >= $awayScore,
            DoubleChanceOutcome::AWAY_OR_DRAW => $awayScore >= $homeScore,
            DoubleChanceOutcome::HOME_OR_AWAY => $homeScore !== $awayScore,
        };
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' => EnumsMarket::HOCKEY_DOUBLE_CHANCE,
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

            foreach (DoubleChanceOutcome::cases() as $outcome) {
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
