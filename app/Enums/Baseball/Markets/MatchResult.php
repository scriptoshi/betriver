<?php

namespace App\Enums\Baseball\Markets;

use App\Contracts\BetMarket;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Baseball\Outcomes\MatchResultOutcome;
use App\Enums\GoalCount;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum MatchResult: string implements BetMarket
{
    case FULL_GAME = 'full_game';
    case FIRST_INNING = 'innings_1';
    case SECOND_INNING = 'innings_2';
    case THIRD_INNING = 'innings_3';
    case FIRST_THREE_INNINGS = 'first_three_innings';
    case FIRST_FIVE_INNINGS = 'first_five_innings';
    case FIRST_SEVEN_INNINGS = 'first_seven_innings';

    public function oddsId(): int
    {
        return match ($this) {
            self::FULL_GAME => 1,
            self::FIRST_INNING => 10,
            self::SECOND_INNING => 39,
            self::THIRD_INNING => 40,
            self::FIRST_THREE_INNINGS => 36,
            self::FIRST_FIVE_INNINGS => 29,
            self::FIRST_SEVEN_INNINGS => 48,
        };
    }

    public function outcomes(): array
    {
        return MatchResultOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::FULL_GAME => "Match Winner",
            self::FIRST_INNING => "1x2 (1st Inning)",
            self::SECOND_INNING => "1x2 (2nd Inning)",
            self::THIRD_INNING => "1x2 (3rd Inning)",
            self::FIRST_THREE_INNINGS => "1x2 (1st 3 Innings)",
            self::FIRST_FIVE_INNINGS => "1x2 (1st 5 Innings)",
            self::FIRST_SEVEN_INNINGS => "1x2 (1st 7 Innings)",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = MatchResultOutcome::from($bet->result);
        $homeScore = $this->getScore($game, GoalCount::HOME);
        $awayScore = $this->getScore($game, GoalCount::AWAY);

        return match ($outcome) {
            MatchResultOutcome::HOME => $homeScore > $awayScore,
            MatchResultOutcome::AWAY => $awayScore > $homeScore,
            MatchResultOutcome::DRAW => $homeScore === $awayScore,
        };
    }

    private function getScore(Game $game, GoalCount $team): int
    {
        return match ($this) {
            self::FULL_GAME => $game->getScores('total', $team),
            self::FIRST_INNING, self::SECOND_INNING, self::THIRD_INNING => $game->getScores($this->value, $team),
            self::FIRST_THREE_INNINGS =>
            $game->getScores('innings_1', $team) +
                $game->getScores('innings_2', $team) +
                $game->getScores('innings_3', $team),
            self::FIRST_FIVE_INNINGS =>
            $game->getScores('innings_1', $team) +
                $game->getScores('innings_2', $team) +
                $game->getScores('innings_3', $team) +
                $game->getScores('innings_4', $team) +
                $game->getScores('innings_5', $team),
            self::FIRST_SEVEN_INNINGS =>
            $game->getScores('innings_1', $team) +
                $game->getScores('innings_2', $team) +
                $game->getScores('innings_3', $team) +
                $game->getScores('innings_4', $team) +
                $game->getScores('innings_5', $team) +
                $game->getScores('innings_6', $team) +
                $game->getScores('innings_7', $team),
        };
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' => EnumsMarket::BASEBALL_MATCH_RESULT,
                    'sport' => LeagueSport::BASEBALL,
                ],
                [
                    'slug' => Str::slug(LeagueSport::BASEBALL->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'name' => self::formatMarketName($case->name()),
                    'sport' => LeagueSport::BASEBALL,
                    'is_default' => $case == self::FULL_GAME,
                ]
            );

            foreach (MatchResultOutcome::cases() as $outcome) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'name' => $outcome->name(),
                    ],
                    ['result' => $outcome->value, 'sport' => LeagueSport::BASEBALL]
                );
            }
        }
    }

    private static function formatMarketName(string $name): string
    {
        return Str::of($name)->replace(['Home', 'Away'], ['{home}', '{away}']);
    }
}
