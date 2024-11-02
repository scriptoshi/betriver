<?php

namespace App\Enums\Baseball\Markets;

use App\Contracts\BetMarket;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Baseball\Outcomes\OddEvenOutcome;
use App\Enums\GoalCount;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;
use App\Enums\MarketCategory;

enum OddEven: string implements BetMarket
{
    case FULL_GAME = 'full_game';
    case FULL_GAME_INCLUDING_OT = 'full_game_including_ot';
    case HOME_TEAM_OT = 'home_team_ot';
    case AWAY_TEAM_OT = 'away_team_ot';

    public function oddsId(): int
    {
        return match ($this) {
            self::FULL_GAME => 57,
            self::FULL_GAME_INCLUDING_OT => 9,
            self::HOME_TEAM_OT => 65,
            self::AWAY_TEAM_OT => 66,
        };
    }

    public function outcomes(): array
    {
        return OddEvenOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::FULL_GAME => "Odd/Even",
            self::FULL_GAME_INCLUDING_OT => "Odd/Even (Including OT)",
            self::HOME_TEAM_OT => "Home Odd/Even (OT)",
            self::AWAY_TEAM_OT => "Away Odd/Even (OT)",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = OddEvenOutcome::from($bet->result);
        $totalScore = $this->getTotalScore($game);

        return ($totalScore % 2 == 0) == ($outcome == OddEvenOutcome::EVEN);
    }

    private function getTotalScore(Game $game): int
    {
        return match ($this) {
            self::FULL_GAME => $game->getScores('total',  GoalCount::TOTAL),
            self::FULL_GAME_INCLUDING_OT => $game->getScores(['total', 'extra'], GoalCount::TOTAL),
            self::HOME_TEAM_OT => $game->getScores('extra', GoalCount::HOME),
            self::AWAY_TEAM_OT => $game->getScores('extra', GoalCount::AWAY),
        };
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' => EnumsMarket::BASEBALL_ODD_EVEN,
                    'sport' => LeagueSport::BASEBALL,
                ],
                [
                    'slug' => Str::slug(LeagueSport::BASEBALL->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'category' => MarketCategory::getCategory(self::class),
                    'name' => self::formatMarketName($case->name()),
                    'sport' => LeagueSport::BASEBALL,
                ]
            );

            foreach (OddEvenOutcome::cases() as $outcome) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'result' => $outcome->value,
                    ],
                    ['name' => $outcome->name(), 'sport' => LeagueSport::BASEBALL]
                );
            }
        }
    }

    private static function formatMarketName(string $name): string
    {
        return Str::of($name)->replace(['Home', 'Away'], ['{home}', '{away}']);
    }
}
