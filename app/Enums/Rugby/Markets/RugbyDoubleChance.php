<?php

namespace App\Enums\Rugby\Markets;

use App\Contracts\BetMarket;
use App\Enums\MarketCategory;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Rugby\Outcomes\RugbyDoubleChanceOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum RugbyDoubleChance: string implements BetMarket
{
    case FULL_TIME = 'full_time';
    case FIRST_HALF = 'first_half';

    public function oddsId(): int
    {
        return match ($this) {
            self::FULL_TIME => 41,
            self::FIRST_HALF => 60,
        };
    }

    public function outcomes(): array
    {
        return RugbyDoubleChanceOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::FULL_TIME => "Double Chance",
            self::FIRST_HALF => "Double Chance - 1st Half",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = RugbyDoubleChanceOutcome::from($bet->result);
        $period = $this == self::FULL_TIME ? 'fulltime' : 'halftime';

        $homeScore = $game->getScores($period, GoalCount::HOME);
        $awayScore = $game->getScores($period, GoalCount::AWAY);

        return match ($outcome) {
            RugbyDoubleChanceOutcome::HOME_DRAW => $homeScore >= $awayScore,
            RugbyDoubleChanceOutcome::HOME_AWAY => $homeScore != $awayScore,
            RugbyDoubleChanceOutcome::DRAW_AWAY => $homeScore <= $awayScore,
        };
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'sport' => LeagueSport::RUGBY
                ],
                [
                    'slug' => Str::slug(LeagueSport::RUGBY->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'category' => MarketCategory::getCategory(self::class),
                    'name' => self::formatMarketName($case->name()),
                    'type' => EnumsMarket::RUGBY_DOUBLE_CHANCE,
                    'sport' => LeagueSport::RUGBY
                ]
            );
            foreach (RugbyDoubleChanceOutcome::cases() as $outcome) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'result' => $outcome->value,
                    ],
                    ['name' => $outcome->name(), 'sport' => LeagueSport::RUGBY]
                );
            }
        }
    }

    private static function formatMarketName(string $name): string
    {
        return Str::of($name)->replace(['Home', 'Away'], ['{home}', '{away}']);
    }
}
