<?php

namespace App\Enums\Basketball\Markets;

use App\Contracts\BetMarket;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Basketball\Outcomes\AsianHandicapOutcome;
use App\Enums\GoalCount;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum AsianHandicap: string implements BetMarket
{
    case FULL_TIME = 'full_time';
    case FIRST_HALF = 'first_half';
    case FIRST_QUARTER = 'first_quarter';
    case SECOND_QUARTER = 'second_quarter';

    public function oddsId(): int
    {
        return match ($this) {
            self::FULL_TIME => 3,
            self::FIRST_HALF => 10,
            self::FIRST_QUARTER => 17,
            self::SECOND_QUARTER => 11,
        };
    }

    public function outcomes(): array
    {
        return AsianHandicapOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::FULL_TIME => "Asian Handicap",
            self::FIRST_HALF => "Asian Handicap First Half",
            self::FIRST_QUARTER => "Asian Handicap 1st Qtr",
            self::SECOND_QUARTER => "Asian Handicap 2nd Qtr",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = AsianHandicapOutcome::from($bet->result);

        $homeScore = $this->getRelevantScore($game, GoalCount::HOME);
        $awayScore = $this->getRelevantScore($game, GoalCount::AWAY);

        $handicapValue = $outcome->handicapValue();
        $adjustedHomeScore = $homeScore + $handicapValue;

        $scoreDifference = $adjustedHomeScore - $awayScore;

        return match ($outcome->team()) {
            'home' => $scoreDifference > 0,
            'away' => $scoreDifference < 0,
        };
    }

    private function getRelevantScore(Game $game, GoalCount $team): int
    {
        return match ($this) {
            self::FULL_TIME => $game->getScores('total', $team),
            self::FIRST_HALF => $game->getScores(['quarter_1', 'quarter_2'], $team),
            self::FIRST_QUARTER => $game->getScores('quarter_1', $team),
            self::SECOND_QUARTER => $game->getScores('quarter_2', $team),
        };
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'sport' => LeagueSport::BASKETBALL
                ],
                [
                    'slug' => Str::slug(LeagueSport::BASKETBALL->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'name' => self::formatMarketName($case->name()),
                    'type' => EnumsMarket::BASKETBALL_ASIAN_HANDICAP,
                    'sport' => LeagueSport::BASKETBALL
                ]
            );
            foreach (AsianHandicapOutcome::cases() as $outcome) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'result' => $outcome->value,
                    ],
                    ['name' => $outcome->name(), 'sport' => LeagueSport::BASKETBALL]
                );
            }
        }
    }

    private static function formatMarketName(string $name): string
    {
        return Str::of($name)->replace(['Home', 'Away'], ['{home}', '{away}']);
    }
}
