<?php

namespace App\Enums\Baseball\Markets;

use App\Contracts\BetMarket;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Baseball\Outcomes\HandicapOutcome;
use App\Enums\GoalCount;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Google\Service\Analytics\Goal;
use Illuminate\Support\Str;

enum Handicap: string implements BetMarket
{
    case FULL_GAME = 'full_game';
    case FIRST_INNING = 'innings_1';
    case FIRST_THREE_INNINGS = 'first_three_innings';
    case FIRST_FIVE_INNINGS = 'first_five_innings';
    case FIRST_SEVEN_INNINGS = 'first_seven_innings';
    case FOUR_HALF_INNINGS = 'four_half_innings';
    case OVERTIME = 'overtime';

    public function oddsId(): int
    {
        return match ($this) {
            self::FULL_GAME => 2,
            self::FIRST_INNING => 12,
            self::FIRST_THREE_INNINGS => 35,
            self::FIRST_FIVE_INNINGS => 3,
            self::FIRST_SEVEN_INNINGS => 46,
            self::FOUR_HALF_INNINGS => 63,
            self::OVERTIME => 13,
        };
    }

    public function outcomes(): array
    {
        return HandicapOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::FULL_GAME => "Asian Handicap",
            self::FIRST_INNING => "Asian Handicap (1st Inning)",
            self::FIRST_THREE_INNINGS => "Asian Handicap (1st 3 Innings)",
            self::FIRST_FIVE_INNINGS => "Asian Handicap (1st 5 Innings)",
            self::FIRST_SEVEN_INNINGS => "Asian Handicap (1st 7 Innings)",
            self::FOUR_HALF_INNINGS => "Asian Handicap (4.5 Innings)",
            self::OVERTIME => "Asian Handicap (OT)",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = HandicapOutcome::from($bet->result);
        $homeScore = $this->getScore($game, GoalCount::HOME);
        $awayScore = $this->getScore($game, GoalCount::AWAY);

        $adjustedHomeScore = $homeScore + $outcome->handicapValue();

        return match ($outcome->team()) {
            'home' => $adjustedHomeScore > $awayScore,
            'away' => $adjustedHomeScore < $awayScore,
        };
    }

    private function getScore(Game $game, GoalCount $team): int
    {
        return match ($this) {
            self::FULL_GAME => $game->getScores('total', $team),
            self::FIRST_INNING => $game->getScores('innings_1', $team),
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
            self::FOUR_HALF_INNINGS =>
            $game->getScores('innings_1', $team) +
                $game->getScores('innings_2', $team) +
                $game->getScores('innings_3', $team) +
                $game->getScores('innings_4', $team) +
                ($game->getScores('innings_5', $team) / 2),
            self::OVERTIME => $game->getScores('extra', $team),
        };
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' => EnumsMarket::BASEBALL_HANDICAP,
                    'sport' => LeagueSport::BASEBALL,
                ],
                [
                    'slug' => Str::slug(LeagueSport::BASEBALL->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'name' => self::formatMarketName($case->name()),
                    'sport' => LeagueSport::BASEBALL,
                ]
            );

            foreach (HandicapOutcome::cases() as $outcome) {
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
