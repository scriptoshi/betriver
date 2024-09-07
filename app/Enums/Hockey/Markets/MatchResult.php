<?php

namespace App\Enums\Hockey\Markets;

use App\Enums\MarketCategory;
use App\Contracts\BetMarket;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Hockey\Outcomes\MatchResultOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum MatchResult: string implements BetMarket
{
    case FULL_TIME = 'total';
    case FIRST_PERIOD = 'first';
    case SECOND_PERIOD = 'second';
    case THIRD_PERIOD = 'third';

    public function oddsId(): int
    {
        return match ($this) {
            self::FULL_TIME => 1,
            self::FIRST_PERIOD => 21,
            self::SECOND_PERIOD => 16,
            self::THIRD_PERIOD => 17,
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
            self::FIRST_PERIOD => "1x2 (1st Period)",
            self::SECOND_PERIOD => "3Way Result (2nd Period)",
            self::THIRD_PERIOD => "3Way Result (3rd Period)",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = MatchResultOutcome::from($bet->result);
        $homeScore = $game->getScores($this->value, GoalCount::HOME);
        $awayScore = $game->getScores($this->value, GoalCount::AWAY);

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
                    'type' => EnumsMarket::HOCKEY_MATCH_RESULT,
                    'sport' => LeagueSport::HOCKEY
                ],
                [
                    'slug' => Str::slug(LeagueSport::HOCKEY->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'category' => MarketCategory::getCategory(self::class),
                    'name' => self::formatMarketName($case->name()),
                    'sport' => LeagueSport::HOCKEY,
                    'is_default' => $case == self::FULL_TIME,
                ]
            );

            foreach (MatchResultOutcome::cases() as $outcome) {
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
