<?php

namespace App\Enums\Handball\Markets;

use App\Enums\MarketCategory;
use App\Contracts\BetMarket;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Soccer\Outcomes\YesNo;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum TeamToScore: string implements BetMarket
{
    case BOTH_TEAMS_TO_SCORE = 'both_teams_to_score';
    case HOME_TEAM_TO_SCORE = 'home_team_to_score';
    case AWAY_TEAM_TO_SCORE = 'away_team_to_score';

    public function oddsId(): int
    {
        return match ($this) {
            self::BOTH_TEAMS_TO_SCORE => 43,
            self::HOME_TEAM_TO_SCORE => 60,
            self::AWAY_TEAM_TO_SCORE => 61,
        };
    }

    public function outcomes(): array
    {
        return YesNo::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::BOTH_TEAMS_TO_SCORE => "Both Teams To Score",
            self::HOME_TEAM_TO_SCORE => "{home} To Score a Goal",
            self::AWAY_TEAM_TO_SCORE => "{away} To Score a Goal",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = YesNo::from($bet->result);
        $homeScore = $game->getScores('total', 'home');
        $awayScore = $game->getScores('total', 'away');
        return match ($this) {
            self::BOTH_TEAMS_TO_SCORE => match ($outcome) {
                YesNo::YES => $homeScore > 0 && $awayScore > 0,
                YesNo::NO => $homeScore === 0 || $awayScore === 0,
            },
            self::HOME_TEAM_TO_SCORE => match ($outcome) {
                YesNo::YES => $homeScore > 0,
                YesNo::NO => $homeScore === 0,
            },
            self::AWAY_TEAM_TO_SCORE => match ($outcome) {
                YesNo::YES => $awayScore > 0,
                YesNo::NO => $awayScore === 0,
            },
        };
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' => EnumsMarket::HANDBALL_TEAM_TO_SCORE,
                    'sport' => LeagueSport::HANDBALL,
                ],
                [
                    'slug' => Str::slug(LeagueSport::HANDBALL->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'category' => MarketCategory::getCategory(self::class),
                    'name' => $case->name(),
                ]
            );

            foreach ($case->outcomes() as $outcome) {
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
