<?php

namespace App\Enums\Soccer\Markets;

use App\Contracts\BetMarket;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Soccer\Outcomes\CorrectScoreOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum CorrectScore: string implements BetMarket
{
    case FULLTIME = 'fulltime';
    case FIRSTHALF = 'firsthalf';
    case SECONDHALF = 'secondhalf';

    public function oddsId(): int
    {
        return match ($this) {
            self::FULLTIME => 10,
            self::FIRSTHALF => 31,
            self::SECONDHALF => 62,
        };
    }

    public function outcomes(): array
    {
        return CorrectScoreOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::FIRSTHALF => "Correct Score - Full Time",
            self::FIRSTHALF => "Correct Score - First Half",
            self::SECONDHALF => "Correct Score - Second Half",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = CorrectScoreOutcome::from($bet->result);
        $homeScore = $game->getScores($this->value, GoalCount::HOME);
        $awayScore = $game->getScores($this->value, GoalCount::AWAY);
        return $outcome->homeGoals() === $homeScore && $outcome->awayGoals() === $awayScore;
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' => EnumsMarket::CORRECT_SCORE,
                    'sport' => LeagueSport::FOOTBALL
                ],
                [
                    'slug' => Str::slug($case->name()),
                    'description' => $case->name(),
                    'name' => self::formatMarketName($case->name()),
                    'sport' => LeagueSport::FOOTBALL
                ]
            );

            foreach (CorrectScoreOutcome::cases() as $outcome) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'name' => $outcome->name(),
                    ],
                    [
                        'result' => $outcome->value,
                        'sport' => LeagueSport::FOOTBALL
                    ]
                );
            }
        }
    }

    private static function formatMarketName(string $name): string
    {
        return Str::of($name)->replace(['Home', 'Away'], ['{home}', '{away}']);
    }
}
