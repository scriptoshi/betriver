<?php

namespace App\Enums\Handball\Markets;

use App\Contracts\BetMarket;
use App\Enums\Handball\Outcomes\MatchResultOutcome;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum MatchResult: string implements BetMarket
{
    case FULL_TIME = 'total';
    case FIRST_HALF = 'firsthalf';
    case SECOND_HALF = 'secondhalf';

    public function oddsId(): int
    {
        return match ($this) {
            self::FULL_TIME => 1,
            self::FIRST_HALF => 12,
            self::SECOND_HALF => 3,
        };
    }

    public function outcomes(): array
    {
        return MatchResultOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::FULL_TIME => "3Way Result",
            self::FIRST_HALF => "1st Half 3Way Result",
            self::SECOND_HALF => "2nd Half 3Way Result",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = MatchResultOutcome::from($bet->result);
        $homeScore = $game->getScores($this->value, 'home');
        $awayScore = $game->getScores($this->value, 'away');

        return match ($outcome) {
            MatchResultOutcome::HOME => $homeScore > $awayScore,
            MatchResultOutcome::AWAY => $awayScore > $homeScore,
            MatchResultOutcome::DRAW => $homeScore === $awayScore,
        };
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' => EnumsMarket::HANDBALL_MATCH_RESULT,
                    'sport' => LeagueSport::HANDBALL,
                ],
                [
                    'slug' => Str::slug(LeagueSport::HANDBALL->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'name' => $case->name(),
                ]
            );

            foreach (MatchResultOutcome::cases() as $outcome) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'name' => $outcome->name(),
                    ],
                    [
                        'result' => $outcome->value,
                        'sport' => LeagueSport::HANDBALL,
                    ]
                );
            }
        }
    }
}
