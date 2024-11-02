<?php

namespace App\Enums\Rugby\Markets;

use App\Contracts\BetMarket;
use App\Enums\MarketCategory;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Rugby\Outcomes\RugbyMatchResultOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum RugbyMatchResult: string implements BetMarket
{
    case FULL_TIME = 'full_time';
    case FIRST_HALF = 'first_half';
    case SECOND_HALF = 'second_half';

    public function oddsId(): int
    {
        return match ($this) {
            self::FULL_TIME => 1,
            self::FIRST_HALF => 8,
            self::SECOND_HALF => 3,
        };
    }

    public function outcomes(): array
    {
        return RugbyMatchResultOutcome::cases();
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
        $outcome = RugbyMatchResultOutcome::from($bet->result);
        $period = match ($this) {
            self::FULL_TIME => 'fulltime',
            self::FIRST_HALF => 'halftime',
            self::SECOND_HALF => 'secondhalf',
        };

        $homeScore = $game->getScores($period, GoalCount::HOME);
        $awayScore = $game->getScores($period, GoalCount::AWAY);

        return match ($outcome) {
            RugbyMatchResultOutcome::HOME => $homeScore > $awayScore,
            RugbyMatchResultOutcome::AWAY => $awayScore > $homeScore,
            RugbyMatchResultOutcome::DRAW => $homeScore == $awayScore,
        };
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'sport' => LeagueSport::RUGBY
                ],
                [
                    'slug' => Str::slug(LeagueSport::RUGBY->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'category' => MarketCategory::getCategory(self::class),
                    'name' => self::formatMarketName($case->name()),
                    'sport' => LeagueSport::RUGBY,
                    'type' => EnumsMarket::RUGBY_MATCH_RESULT,
                    'is_default' => $case == self::FULL_TIME,
                ]
            );
            foreach (RugbyMatchResultOutcome::cases() as $outcome) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'result' => $outcome->value,
                    ],
                    ['name' => $outcome->name(), 'sport' => LeagueSport::RUGBY]
                );
            }
        }
    }

    private static function formatMarketName(string $name): string
    {
        return Str::of($name)->replace(['Home', 'Away'], ['{home}', '{away}']);
    }
}
