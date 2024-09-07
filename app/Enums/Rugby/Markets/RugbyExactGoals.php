<?php

namespace App\Enums\Rugby\Markets;

use App\Contracts\BetMarket;
use App\Enums\MarketCategory;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Rugby\Outcomes\RugbyExactGoalsOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum RugbyExactGoals: string implements BetMarket
{
    case HOME_TEAM = 'home_team';
    case AWAY_TEAM = 'away_team';

    public function oddsId(): int
    {
        return match ($this) {
            self::HOME_TEAM => 44,
            self::AWAY_TEAM => 45,
        };
    }

    public function outcomes(): array
    {
        return RugbyExactGoalsOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::HOME_TEAM => "{home} Team Exact Goals Number",
            self::AWAY_TEAM => "{away} Team Exact Goals Number",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = RugbyExactGoalsOutcome::from($bet->result);
        $team = $this === self::HOME_TEAM ? GoalCount::HOME : GoalCount::AWAY;
        $goals = $game->getScores('fulltime', $team);

        if ($outcome === RugbyExactGoalsOutcome::SIX_PLUS) {
            return $goals >= 6;
        }

        return $goals === $outcome->value();
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
                    'sport' => LeagueSport::RUGBY,
                    'type' => EnumsMarket::RUGBY_EXACT_GOALS
                ]
            );
            foreach (RugbyExactGoalsOutcome::cases() as $outcome) {
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
