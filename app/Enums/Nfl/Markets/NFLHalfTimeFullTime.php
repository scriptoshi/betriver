<?php

namespace App\Enums\Nfl\Markets;

use App\Contracts\BetMarket;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Afl\Outcomes\AFLHalfTimeFullTimeOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum NFLHalfTimeFullTime: string implements BetMarket
{
    case FULL_GAME = 'full_game';

    public function oddsId(): int
    {
        return 5; // The ID provided for HT/FT Double in NFL
    }

    public function outcomes(): array
    {
        return AFLHalfTimeFullTimeOutcome::cases();
    }

    public function name(): string
    {
        return "HT/FT Double";
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = AFLHalfTimeFullTimeOutcome::from($bet->result);

        $halfTimeHomeScore = $game->getScores('halftime', GoalCount::HOME);
        $halfTimeAwayScore = $game->getScores('halftime', GoalCount::AWAY);
        $fullTimeHomeScore = $game->getScores('fulltime', GoalCount::HOME);
        $fullTimeAwayScore = $game->getScores('fulltime', GoalCount::AWAY);

        $halfTimeResult = $this->getResult($halfTimeHomeScore, $halfTimeAwayScore);
        $fullTimeResult = $this->getResult($fullTimeHomeScore, $fullTimeAwayScore);

        return $outcome->halfTimeResult() === $halfTimeResult && $outcome->fullTimeResult() === $fullTimeResult;
    }

    private function getResult(int $homeScore, int $awayScore): string
    {
        if ($homeScore > $awayScore) {
            return 'H';
        } elseif ($awayScore > $homeScore) {
            return 'A';
        } else {
            return 'D';
        }
    }

    public static function seed(): void
    {
        $market = Market::updateOrCreate(
            [
                'segment' => self::FULL_GAME->value,
                'oddsId' => self::FULL_GAME->oddsId(),
                'sport' => LeagueSport::NFL
            ],
            [
                'slug' => Str::slug(LeagueSport::NFL->value . '-' . self::FULL_GAME->name()),
                'description' => self::FULL_GAME->name(),
                'name' => self::FULL_GAME->name(),
                'type' => EnumsMarket::NFL_HALF_TIME_FULL_TIME,
                'sport' => LeagueSport::NFL
            ]
        );
        foreach (AFLHalfTimeFullTimeOutcome::cases() as $outcome) {
            Bet::updateOrCreate(
                [
                    'market_id' => $market->id,
                    'result' => $outcome->value,
                ],
                ['name' => $outcome->name(), 'sport' => LeagueSport::NFL]
            );
        }
    }
}
