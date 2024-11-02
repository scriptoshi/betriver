<?php

namespace App\Enums\Rugby\Markets;

use App\Contracts\BetMarket;
use App\Enums\MarketCategory;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Rugby\Outcomes\RugbyHandicapResultOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use App\Traits\Handicaps;
use Illuminate\Support\Str;

enum RugbyHandicapResult: string implements BetMarket
{
    use Handicaps;
    case FULL_TIME = 'full_time';
    case FIRST_HALF = 'first_half';

    public function oddsId(): int
    {
        return match ($this) {
            self::FULL_TIME => 6,
            self::FIRST_HALF => 9,
        };
    }

    public function outcomes(): array
    {
        return RugbyHandicapResultOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::FULL_TIME => "Handicap Result",
            self::FIRST_HALF => "Handicap Result 1st Half",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = RugbyHandicapResultOutcome::from($bet->result);
        $period = $this == self::FULL_TIME ? 'fulltime' : 'halftime';

        $homeScore = $game->getScores($period, GoalCount::HOME);
        $awayScore = $game->getScores($period, GoalCount::AWAY);

        $handicapValue = $outcome->handicapValue();
        $adjustedHomeScore = $homeScore + $handicapValue;

        $result = match (true) {
            $adjustedHomeScore > $awayScore => 'home',
            $adjustedHomeScore < $awayScore => 'away',
            default => 'draw',
        };

        return $outcome->result() == $result;
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
                    'type' => EnumsMarket::RUGBY_HANDICAP_RESULT,
                    'sport' => LeagueSport::RUGBY
                ]
            );
            foreach (RugbyHandicapResultOutcome::cases() as $outcome) {
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
