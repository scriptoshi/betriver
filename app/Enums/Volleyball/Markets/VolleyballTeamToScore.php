<?php

namespace App\Enums\Volleyball\Markets;

use App\Contracts\BetMarket;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Soccer\Outcomes\YesNo;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum VolleyballTeamToScore: string implements BetMarket
{
    case HOME = 'home';
    case AWAY = 'away';

    public function oddsId(): int
    {
        return match ($this) {
            self::HOME => 62, // Assuming 62 is the ID for "Home Team Score a Goal"
            self::AWAY => 63, // Assuming 63 is the ID for "Away Team Score a Goal"
        };
    }

    public function outcomes(): array
    {
        return YesNo::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::HOME => "{home} To Win A Set",
            self::AWAY => "{away} To Win A Set",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = YesNo::from($bet->result);
        $teamSets = match ($this) {
            self::HOME => $game->getScores('fulltime', GoalCount::HOME),
            self::AWAY => $game->getScores('fulltime', GoalCount::AWAY),
        };

        $teamWonSet = $teamSets > 0;

        return match ($outcome) {
            YesNo::YES => $teamWonSet,
            YesNo::NO => !$teamWonSet,
        };
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'sport' => LeagueSport::VOLLEYBALL
                ],
                [
                    'slug' => Str::slug(LeagueSport::VOLLEYBALL->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'name' => self::formatMarketName($case->name()),
                    'type' => EnumsMarket::VOLLEYBALL_TEAM_TO_SCORE,
                    'sport' => LeagueSport::VOLLEYBALL
                ]
            );
            foreach (YesNo::cases() as $outcome) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'name' => $outcome->name(),
                    ],
                    ['result' => $outcome->value, 'sport' => LeagueSport::VOLLEYBALL]
                );
            }
        }
    }

    private static function formatMarketName(string $name): string
    {
        return Str::of($name)->replace(['Home', 'Away'], ['{home}', '{away}']);
    }
}
