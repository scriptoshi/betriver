<?php

namespace App\Enums\Afl\Markets;

use App\Contracts\BetMarket;
use App\Enums\Afl\Outcomes\AFLTotalScoresOutcome;
use App\Enums\Afl\ScoreType;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;
use App\Enums\MarketCategory;

enum AFLTotalScores: string implements BetMarket
{
    case TOTAL_BEHINDS = 'total_behinds';
    case HOME_TOTAL_GOALS = 'home_total_goals';
    case AWAY_TOTAL_GOALS = 'away_total_goals';
    case HOME_TOTAL_BEHINDS = 'home_total_behinds';
    case AWAY_TOTAL_BEHINDS = 'away_total_behinds';

    public function oddsId(): int
    {
        return match ($this) {
            self::TOTAL_BEHINDS => 44,
            self::HOME_TOTAL_GOALS => 45,
            self::AWAY_TOTAL_GOALS => 46,
            self::HOME_TOTAL_BEHINDS => 47,
            self::AWAY_TOTAL_BEHINDS => 48
        };
    }

    public function outcomes(): array
    {
        return AFLTotalScoresOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::TOTAL_BEHINDS => "Total Behinds",
            self::HOME_TOTAL_GOALS => "{home} Total Goals",
            self::AWAY_TOTAL_GOALS => "{away} Total Goals",
            self::HOME_TOTAL_BEHINDS => "{home} Total Behinds",
            self::AWAY_TOTAL_BEHINDS => "{away} Total Behinds"
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = AFLTotalScoresOutcome::from($bet->result);

        $totalScore = match ($this) {
            self::TOTAL_BEHINDS => $game->getScores(ScoreType::fulltime('behinds'), GoalCount::TOTAL),
            self::HOME_TOTAL_GOALS => $game->getScores(ScoreType::fulltime('goals'), GoalCount::HOME),
            self::AWAY_TOTAL_GOALS => $game->getScores(ScoreType::fulltime('goals'), GoalCount::AWAY),
            self::HOME_TOTAL_BEHINDS => $game->getScores(ScoreType::fulltime('behinds'), GoalCount::HOME),
            self::AWAY_TOTAL_BEHINDS => $game->getScores(ScoreType::fulltime('behinds'), GoalCount::AWAY),
        };

        return match ($outcome->type()) {
            'over' => $totalScore > $outcome->value(),
            'under' => $totalScore < $outcome->value(),
        };
    }


    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'sport' => LeagueSport::AFL
                ],
                [
                    'slug' => Str::slug(LeagueSport::AFL->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'category' => MarketCategory::getCategory(self::class),
                    'name' => $case->name(),
                    'sport' => LeagueSport::AFL,
                    'type' => EnumsMarket::AFL_TOTAL_SCORES //'AFLTotalScores',

                ]
            );
            foreach (AFLTotalScoresOutcome::cases() as $outcome) {
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
}
