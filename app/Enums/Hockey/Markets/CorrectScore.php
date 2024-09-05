<?php

namespace App\Enums\Hockey\Markets;

use App\Contracts\BetMarket;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Hockey\Outcomes\CorrectScoreOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum CorrectScore: string implements BetMarket
{
    case FULL_TIME = 'total';
    case FIRST_PERIOD = 'first';
    case SECOND_PERIOD = 'second';
    case THIRD_PERIOD = 'third';

    public function oddsId(): int
    {
        return match ($this) {
            self::FULL_TIME => 74,
            self::FIRST_PERIOD => 35,
            self::SECOND_PERIOD => 39,
            self::THIRD_PERIOD => 40,
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
            self::FIRST_PERIOD => "Correct Score (1st Period)",
            self::SECOND_PERIOD => "Correct Score (2nd Period)",
            self::THIRD_PERIOD => "Correct Score (3rd Period)",
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
                    'sport' => LeagueSport::HOCKEY
                ],
                [
                    'slug' => Str::slug(LeagueSport::HOCKEY->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'name' => self::formatMarketName($case->name()),
                    'sport' => LeagueSport::HOCKEY
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
                        'sport' => LeagueSport::HOCKEY
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
