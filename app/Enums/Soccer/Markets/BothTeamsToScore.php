<?php

namespace App\Enums\Soccer\Markets;

use App\Contracts\BetMarket;
use App\Enums\MarketCategory;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Soccer\Outcomes\YesNo;
use App\Enums\ScoreType;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum BothTeamsToScore: string implements BetMarket
{
    case FULLTIME = 'fulltime';
    case FIRSTHALF = 'halftime';
    case SECONDHALF = 'secondhalf';
    case FIRST_SECOND = 'first_second';
    case BOTH = 'both';

    public function oddsId(): ?int
    {
        return match ($this) {
            self::FULLTIME => 8,
            self::FIRSTHALF => 34,
            self::SECONDHALF => 35,
            self::FIRST_SECOND => 73,
            self::BOTH => 113,
        };
    }

    public function outcomes(): array
    {
        return YesNo::cases();
    }


    public function name(): string
    {
        return match ($this) {
            self::FULLTIME => "Both Teams Score",
            self::FIRSTHALF => "Both Teams Score - First Half",
            self::SECONDHALF => "Both Teams To Score - Second Half",
            self::FIRST_SECOND => "Both Teams to Score 1st Half - 2nd Half",
            self::BOTH => "Both Teams To Score in Both Halves",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = YesNo::from($bet->result);
        $bothScored = $this->didBothTeamsScore($game);
        return $outcome === YesNo::YES ? $bothScored : !$bothScored;
    }

    private function didBothTeamsScore(Game $game): bool
    {
        return match ($this) {
            self::FULLTIME, self::FIRSTHALF, self::SECONDHALF => $this->scoredInPeriod($game, $this->value),
            self::BOTH => $this->scoredInBothHalves($game),
            self::FIRST_SECOND => $this->scoredInFirstAndSecond($game),
        };
    }

    private function scoredInPeriod(Game $game, string $period): bool
    {
        $homeScore = $game->getScores($period, GoalCount::HOME);
        $awayScore = $game->getScores($period, GoalCount::AWAY);
        return $homeScore > 0 && $awayScore > 0;
    }

    private function scoredInBothHalves(Game $game): bool
    {
        return $this->scoredInPeriod($game, ScoreType::HALFTIME->value)
            && $this->scoredInPeriod($game, ScoreType::SECONDHALF->value);
    }

    private function scoredInFirstAndSecond(Game $game): bool
    {
        return $this->scoredInPeriod($game, ScoreType::HALFTIME->value)
            && $this->scoredInPeriod($game, ScoreType::SECONDHALF->value);
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' => EnumsMarket::BOTH_TEAMS_TO_SCORE,
                    'sport' => LeagueSport::FOOTBALL
                ],
                [
                    'slug' => Str::slug($case->name()),
                    'description' => $case->name(),
                    'category' => MarketCategory::getCategory(self::class),
                    'name' => self::formatMarketName($case->name()),
                    'sport' => LeagueSport::FOOTBALL
                ]
            );
            foreach (YesNo::cases() as $bet) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'result' => $bet->value
                    ],
                    [
                        'name' => Str::ucfirst($bet->value),
                        'sport' => LeagueSport::FOOTBALL
                    ]
                );
            }
        }
    }

    private static function formatMarketName(string $name): string
    {
        return Str::of($name)->replace(
            ['Home Team', 'Away Team', 'Home', 'Away', 'Both Teams', 'Either Teams'],
            ['{home}', '{away}', '{home}', '{away}', '{home} and {away}', 'Either {home} OR {away}']
        );
    }
}
