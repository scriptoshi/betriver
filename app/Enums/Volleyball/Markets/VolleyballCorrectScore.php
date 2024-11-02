<?php

namespace App\Enums\Volleyball\Markets;

use App\Enums\MarketCategory;
use App\Contracts\BetMarket;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Volleyball\Outcomes\VolleyballCorrectScoreOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum VolleyballCorrectScore: string implements BetMarket
{
    case FULL_MATCH = 'full_match';

    public function oddsId(): int
    {
        return 4; // Assuming 4 is the ID for "Correct Score"
    }

    public function outcomes(): array
    {
        return VolleyballCorrectScoreOutcome::cases();
    }

    public function name(): string
    {
        return "Correct Score";
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = VolleyballCorrectScoreOutcome::from($bet->result);
        $homeSets = $game->getScores('fulltime', GoalCount::HOME);
        $awaySets = $game->getScores('fulltime', GoalCount::AWAY);

        return $outcome->homeSets() == $homeSets && $outcome->awaySets() == $awaySets;
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
                    'category' => MarketCategory::getCategory(self::class),
                    'name' => self::formatMarketName($case->name()),
                    'type' => EnumsMarket::VOLLEYBALL_CORRECT_SCORE,
                    'sport' => LeagueSport::VOLLEYBALL
                ]
            );
            foreach (VolleyballCorrectScoreOutcome::cases() as $outcome) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'result' => $outcome->value,
                    ],
                    ['name' => $outcome->name(),  'sport' => LeagueSport::VOLLEYBALL]
                );
            }
        }
    }

    private static function formatMarketName(string $name): string
    {
        return Str::of($name)->replace(['Home', 'Away'], ['{home}', '{away}']);
    }
}
