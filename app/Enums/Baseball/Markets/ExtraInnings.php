<?php

namespace App\Enums\Baseball\Markets;

use App\Contracts\BetMarket;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Baseball\Outcomes\YesNoOutcome;
use App\Enums\GoalCount;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum ExtraInnings: string implements BetMarket
{
    case WILL_THERE_BE_EXTRA_INNINGS = 'will_there_be_extra_innings';

    public function oddsId(): int
    {
        return 32;
    }

    public function outcomes(): array
    {
        return YesNoOutcome::cases();
    }

    public function name(): string
    {
        return "Will there be an extra inning?";
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = YesNoOutcome::from($bet->result);
        $hasExtraInnings = $this->hasExtraInnings($game);

        return match ($outcome) {
            YesNoOutcome::YES => $hasExtraInnings,
            YesNoOutcome::NO => !$hasExtraInnings,
        };
    }

    private function hasExtraInnings(Game $game): bool
    {
        return $game->getScores('extra', GoalCount::HOME) !== null || $game->getScores('extra', GoalCount::AWAY) !== null;
    }

    public static function seed(): void
    {
        $case = self::WILL_THERE_BE_EXTRA_INNINGS;
        $market = Market::updateOrCreate(
            [
                'segment' => $case->value,
                'oddsId' => $case->oddsId(),
                'type' => EnumsMarket::BASEBALL_EXTRA_INNINGS,
                'sport' => LeagueSport::BASEBALL,
            ],
            [
                'slug' => Str::slug($case->name()),
                'description' => $case->name(),
                'name' => self::formatMarketName($case->name()),
                'sport' => LeagueSport::BASEBALL,
            ]
        );

        foreach (YesNoOutcome::cases() as $outcome) {
            Bet::updateOrCreate(
                [
                    'market_id' => $market->id,
                    'result' => $outcome->value,
                ],
                ['name' => $outcome->name(), 'sport' => LeagueSport::BASEBALL]
            );
        }
    }

    private static function formatMarketName(string $name): string
    {
        return $name;
    }
}
