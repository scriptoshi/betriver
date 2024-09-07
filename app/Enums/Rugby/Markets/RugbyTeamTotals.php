<?php

namespace App\Enums\Rugby\Markets;

use App\Contracts\BetMarket;
use App\Enums\MarketCategory;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Rugby\Outcomes\RugbyTeamTotalsOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum RugbyTeamTotals: string implements BetMarket
{
    case HOME_FULL_TIME = 'home_full_time';
    case AWAY_FULL_TIME = 'away_full_time';
    case HOME_FIRST_HALF = 'home_first_half';
    case AWAY_FIRST_HALF = 'away_first_half';
    case HOME_SECOND_HALF = 'home_second_half';
    case AWAY_SECOND_HALF = 'away_second_half';

    public function oddsId(): int
    {
        return match ($this) {
            self::HOME_FULL_TIME => 26,
            self::AWAY_FULL_TIME => 27,
            self::HOME_FIRST_HALF => 31,
            self::AWAY_FIRST_HALF => 32,
            self::HOME_SECOND_HALF => 33,
            self::AWAY_SECOND_HALF => 34,
        };
    }

    public function outcomes(): array
    {
        return RugbyTeamTotalsOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::HOME_FULL_TIME => "Total - {home}",
            self::AWAY_FULL_TIME => "Total - {away}",
            self::HOME_FIRST_HALF => "{home} Team Total Points (1st Half)",
            self::AWAY_FIRST_HALF => "{away} Team Total Points (1st Half)",
            self::HOME_SECOND_HALF => "{home} Team Total Points (2nd Half)",
            self::AWAY_SECOND_HALF => "{away} Team Total Points (2nd Half)",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = RugbyTeamTotalsOutcome::from($bet->result);

        $team = Str::startsWith($this->value, 'home') ? GoalCount::HOME : GoalCount::AWAY;
        $period = match ($this) {
            self::HOME_FULL_TIME, self::AWAY_FULL_TIME => 'fulltime',
            self::HOME_FIRST_HALF, self::AWAY_FIRST_HALF => 'halftime',
            self::HOME_SECOND_HALF, self::AWAY_SECOND_HALF => 'secondhalf',
        };

        $score = $game->getScores($period, $team);

        return match ($outcome->type()) {
            'over' => $score > $outcome->value(),
            'under' => $score < $outcome->value(),
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
                    'name' => $case->name(),
                    'sport' => LeagueSport::RUGBY,
                    'type' => EnumsMarket::RUGBY_TEAM_TOTALS
                ]
            );
            foreach (RugbyTeamTotalsOutcome::cases() as $outcome) {
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
}
