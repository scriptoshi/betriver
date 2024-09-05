<?php

namespace App\Enums\Basketball\Markets;

use App\Contracts\BetMarket;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Basketball\Outcomes\MatchWinnerOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum MatchWinner: string implements BetMarket
{
    case FULL_TIME = 'fulltime';
    case FIRST_HALF = 'halftime';
    case FIRST_QUARTER = 'quarter_1';
    case SECOND_QUARTER = 'quarter_2';
    case THIRD_QUARTER = 'quarter_3';
    case FOURTH_QUARTER = 'quarter_4';

    public function oddsId(): int
    {
        return match ($this) {
            self::FULL_TIME => 1,
            self::FIRST_HALF => 8,
            self::FIRST_QUARTER => 14,
            self::SECOND_QUARTER => 19,
            self::THIRD_QUARTER => 20,
            self::FOURTH_QUARTER => 21,
        };
    }

    public function outcomes(): array
    {
        return MatchWinnerOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::FULL_TIME => "3Way Result",
            self::FIRST_HALF => "1st Half 3Way Result",
            self::FIRST_QUARTER => "3Way Result - 1st Qtr",
            self::SECOND_QUARTER => "3Way Result - 2nd Qtr",
            self::THIRD_QUARTER => "3Way Result - 3rd Qtr",
            self::FOURTH_QUARTER => "3Way Result - 4th Qtr",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = MatchWinnerOutcome::from($bet->result);
        $homeScore = $game->getRelevantScore($game, GoalCount::HOME);
        $awayScore = $game->getRelevantScore($game, GoalCount::AWAY);

        return match ($outcome) {
            MatchWinnerOutcome::HOME => $homeScore > $awayScore,
            MatchWinnerOutcome::AWAY => $awayScore > $homeScore,
            MatchWinnerOutcome::DRAW => $homeScore === $awayScore,
        };
    }

    private function getRelevantScore(Game $game, string $team): int
    {
        return match ($this) {
            self::FULL_TIME => $game->getScores('total', $team),
            self::FIRST_HALF => $game->getScores(['quarter_1', 'quarter_2'], $team),
            default => $game->getScores($this->value, $team)
        };
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' => EnumsMarket::BASKETBALL_MATCH_WINNER,
                    'sport' => LeagueSport::BASKETBALL,
                ],
                [
                    'slug' => Str::slug(LeagueSport::BASKETBALL->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'name' => self::formatMarketName($case->name()),
                    'sport' => LeagueSport::BASKETBALL,
                    'is_default' => $case == self::FULL_TIME,
                ]
            );

            foreach (MatchWinnerOutcome::cases() as $outcome) {
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
