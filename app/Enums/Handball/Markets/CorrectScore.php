<?php

namespace App\Enums\Handball\Markets;

use App\Contracts\BetMarket;
use App\Enums\Handball\Outcomes\CorrectScoreOutcome;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum CorrectScore: string implements BetMarket
{
    case FULL_TIME = 'total';
    case FIRST_HALF = 'firsthalf';

    public function oddsId(): int
    {
        return match ($this) {
            self::FULL_TIME => 46,
            self::FIRST_HALF => 47,
        };
    }

    public function outcomes(): array
    {
        return CorrectScoreOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::FULL_TIME => "Correct Score",
            self::FIRST_HALF => "Correct Score 1st Half",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = CorrectScoreOutcome::from($bet->result);

        $homeScore = $game->getScores($this->value, 'home');
        $awayScore = $game->getScores($this->value, 'away');

        if ($outcome === CorrectScoreOutcome::ANY_OTHER_HOME_WIN) {
            return $homeScore > $awayScore && !in_array("{$homeScore}-{$awayScore}", array_column(CorrectScoreOutcome::cases(), 'value'));
        }

        if ($outcome === CorrectScoreOutcome::ANY_OTHER_AWAY_WIN) {
            return $awayScore > $homeScore && !in_array("{$homeScore}-{$awayScore}", array_column(CorrectScoreOutcome::cases(), 'value'));
        }

        return $homeScore === $outcome->homeGoals() && $awayScore === $outcome->awayGoals();
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' => EnumsMarket::HANDBALL_CORRECT_SCORE,
                    'sport' => LeagueSport::HANDBALL,
                ],
                [
                    'slug' => Str::slug(LeagueSport::HANDBALL->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'name' => $case->name(),
                ]
            );

            foreach (CorrectScoreOutcome::cases() as $outcome) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'result' => $outcome->value,
                    ],
                    [
                        'name' => $outcome->name(),
                        'sport' => LeagueSport::HANDBALL,
                    ]
                );
            }
        }
    }
}
