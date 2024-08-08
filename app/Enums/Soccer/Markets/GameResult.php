<?php

namespace App\Enums\Soccer\Markets;

use App\Contracts\BetMarket;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Soccer\Outcomes\MatchWinner;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum GameResult: string implements BetMarket
{
    case FULLTIME = 'fulltime';
    case FIRSTHALF = 'halftime';
    case SECONDHALF = 'secondhalf';



    public function oddsId(): ?int
    {
        return match ($this) {
            self::FULLTIME => 1,
            self::FIRSTHALF => 13,
            self::SECONDHALF => 3,
        };
    }

    /**
     * possible outcomes
     */
    public function outcomes(): array
    {
        return MatchWinner::cases(); // cases : HOME, AWAY, DRAW
    }


    public function name(): string
    {
        return match ($this) {
            self::FULLTIME => "Match Winner",
            self::FIRSTHALF => "First Half Winner",
            self::SECONDHALF => "Second Half Winner"
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = MatchWinner::from($bet->result);
        $homeScore = $game->getScores($this->value, GoalCount::HOME);
        $awayScore = $game->getScores($this->value, GoalCount::AWAY);
        return match ($outcome) {
            MatchWinner::AWAY => $awayScore > $homeScore,
            MatchWinner::AWAY => $awayScore < $homeScore,
            MatchWinner::DRAW => $awayScore == $homeScore,
        };
    }


    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' => EnumsMarket::GAME_RESULT,
                    'sport' => LeagueSport::FOOTBALL
                ],
                [
                    'slug' => Str::slug($case->name()),
                    'description' => $case->name(),
                    'name' => self::formatMarketName($case->name()),
                    'sport' => LeagueSport::FOOTBALL
                ]
            );
            foreach (MatchWinner::cases() as $bet) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'name' => formatName($bet->value),
                    ],
                    ['result' => $bet->value, 'sport' => LeagueSport::FOOTBALL]
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
