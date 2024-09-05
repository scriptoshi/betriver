<?php

namespace App\Enums\Afl\Markets;

use App\Contracts\BetMarket;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Afl\Outcomes\AFLOddEvenOutcome;
use App\Enums\Afl\ScoreType;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum AFLOddEven: string implements BetMarket
{
    case FULL_GAME = 'full_game';

    public function oddsId(): int
    {
        return 11; // The ID provided for Odd/Even Match Total
    }

    public function outcomes(): array
    {
        return AFLOddEvenOutcome::cases();
    }

    public function name(): string
    {
        return "Odd/Even Match Total";
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = AFLOddEvenOutcome::from($bet->result);

        $homeScore = $game->getScores(ScoreType::fulltime(), GoalCount::HOME);
        $awayScore = $game->getScores(ScoreType::fulltime(), GoalCount::AWAY);
        $totalScore = $homeScore + $awayScore;

        $isOdd = $totalScore % 2 !== 0;

        return match ($outcome) {
            AFLOddEvenOutcome::ODD => $isOdd,
            AFLOddEvenOutcome::EVEN => !$isOdd,
        };
    }

    public static function seed(): void
    {
        $market = Market::updateOrCreate(
            [
                'segment' => self::FULL_GAME->value,
                'oddsId' => self::FULL_GAME->oddsId(),
                'sport' => LeagueSport::AFL
            ],
            [
                'slug' => Str::slug(LeagueSport::AFL->value . '-' . self::FULL_GAME->name()),
                'description' => self::FULL_GAME->name(),
                'name' => self::FULL_GAME->name(),
                'sport' => LeagueSport::AFL,
                'type' => EnumsMarket::AFL_ODD_EVEN //'AFLOddEven',
            ]
        );
        foreach (AFLOddEvenOutcome::cases() as $outcome) {
            Bet::updateOrCreate(
                [
                    'market_id' => $market->id,
                    'result' => $outcome->value,
                ],
                ['name' => $outcome->name(), 'sport' => LeagueSport::AFL]
            );
        }
    }
}
