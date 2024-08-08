<?php

namespace App\Enums\Hockey\Markets;

use App\Contracts\BetMarket;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Hockey\Outcomes\HandicapOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum Handicap: string implements BetMarket
{
    case FULL_TIME = 'total';
    case FIRST_PERIOD = 'first';
    case SECOND_PERIOD = 'second';
    case THIRD_PERIOD = 'third';

    public function oddsId(): int
    {
        return match ($this) {
            self::FULL_TIME => 3,
            self::FIRST_PERIOD => 48,
            self::SECOND_PERIOD => 49,
            self::THIRD_PERIOD => 50,
        };
    }

    public function outcomes(): array
    {
        return HandicapOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::FULL_TIME => "Asian Handicap",
            self::FIRST_PERIOD => "European Handicap (1st Period)",
            self::SECOND_PERIOD => "European Handicap (2nd Period)",
            self::THIRD_PERIOD => "European Handicap (3rd Period)",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = HandicapOutcome::from($bet->result);
        $homeScore = $game->getScores($this->value, GoalCount::HOME);
        $awayScore = $game->getScores($this->value, GoalCount::AWAY);

        $adjustedHomeScore = $homeScore + $outcome->handicap();

        return match ($outcome->team()) {
            'home' => $adjustedHomeScore > $awayScore,
            'away' => $adjustedHomeScore < $awayScore,
            'draw' => $adjustedHomeScore === $awayScore,
        };
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' => EnumsMarket::HOCKEY_HANDICAP,
                    'sport' => LeagueSport::HOCKEY
                ],
                [
                    'slug' => Str::slug(LeagueSport::HOCKEY->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'name' => self::formatMarketName($case->name()),
                    'sport' => LeagueSport::HOCKEY
                ]
            );

            foreach (HandicapOutcome::cases() as $outcome) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'name' => $outcome->name(),
                    ],
                    [
                        'result' => $outcome->value,
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
