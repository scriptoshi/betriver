<?php

namespace App\Enums\Afl\Markets;

use App\Contracts\BetMarket;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Afl\Outcomes\AFLHalfTimeFullTimeOutcome;
use App\Enums\Afl\ScoreType;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;
use App\Enums\MarketCategory;

enum AFLHalfTimeFullTime: string implements BetMarket
{
    case FULL_GAME = 'full_game';

    public function oddsId(): int
    {
        return 6; // The ID provided for HT/FT Double
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

        $halfTimeHomeScore = $game->getScores(ScoreType::firsthalf(), GoalCount::HOME);
        $halfTimeAwayScore = $game->getScores(ScoreType::firsthalf(), GoalCount::AWAY);
        $fullTimeHomeScore = $game->getScores(ScoreType::fulltime(), GoalCount::HOME);
        $fullTimeAwayScore = $game->getScores(ScoreType::fulltime(), GoalCount::AWAY);

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
                'sport' => LeagueSport::AFL
            ],
            [
                'slug' => Str::slug(LeagueSport::AFL->value . '-' . self::FULL_GAME->name()),
                'description' => self::FULL_GAME->name(),
                'name' => self::FULL_GAME->name(),
                'type' => EnumsMarket::AFL_HALF_TIME_FULL_TIME, ///'AFLHalfTimeFullTime',
                'sport' => LeagueSport::AFL,
                'category' => MarketCategory::getCategory(self::class),
            ]
        );
        foreach (AFLHalfTimeFullTimeOutcome::cases() as $outcome) {
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
