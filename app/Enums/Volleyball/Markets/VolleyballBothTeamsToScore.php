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

enum VolleyballBothTeamsToScore: string implements BetMarket
{
    case FULL_MATCH = 'full_match';

    public function oddsId(): int
    {
        return 39; // Assuming 39 is the ID for "Both Teams To Score"
    }

    public function outcomes(): array
    {
        return YesNo::cases();
    }

    public function name(): string
    {
        return "Both Teams To Win A Set";
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = YesNo::from($bet->result);
        $homeSets = $game->getScores('fulltime', GoalCount::HOME);
        $awaySets = $game->getScores('fulltime', GoalCount::AWAY);

        $bothTeamsWonSet = $homeSets > 0 && $awaySets > 0;

        return match ($outcome) {
            YesNo::YES => $bothTeamsWonSet,
            YesNo::NO => !$bothTeamsWonSet,
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
                    'type' => EnumsMarket::VOLLEYBALL_BOTH_TEAMS_TO_SCORE,
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
