<?php

namespace App\Enums\Basketball\Markets;

use App\Enums\MarketCategory;
use App\Contracts\BetMarket;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Basketball\Outcomes\HTFTDoubleOutcome;
use App\Enums\GoalCount;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum HTFTDouble: string implements BetMarket
{
    case HT_FT = 'ht_ft';

    public function oddsId(): int
    {
        return 15;
    }

    public function outcomes(): array
    {
        return HTFTDoubleOutcome::cases();
    }

    public function name(): string
    {
        return "HT/FT (Including OT)";
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = HTFTDoubleOutcome::from($bet->result);
        $htResult = $this->getResult($game, ['quarter_1', 'quarter_2']);
        $ftResult = $this->getResult($game, 'total');

        return $outcome->halfTimeResult() === $htResult && $outcome->fullTimeResult() === $ftResult;
    }

    private function getResult(Game $game, array|string $period): string
    {
        $homeScore = $game->getScore($period, GoalCount::HOME);
        $awayScore = $game->getScore($period, GoalCount::AWAY);
        return match (true) {
            $homeScore > $awayScore => 'home',
            $awayScore > $homeScore => 'away',
            default => 'draw',
        };
    }

    public static function seed(): void
    {
        $market = Market::updateOrCreate(
            [
                'segment' => self::HT_FT->value,
                'oddsId' => self::HT_FT->oddsId(),
                'sport' => LeagueSport::BASKETBALL
            ],
            [
                'slug' => Str::slug(LeagueSport::BASKETBALL->value . '-' . self::HT_FT->name()),
                'description' => self::HT_FT->name(),
                'category' => MarketCategory::getCategory(self::class),
                'name' => self::formatMarketName(self::HT_FT->name()),
                'type' => EnumsMarket::BASKETBALL_HTFT_DOUBLE,
                'sport' => LeagueSport::BASKETBALL
            ]
        );
        foreach (HTFTDoubleOutcome::cases() as $outcome) {
            Bet::updateOrCreate(
                [
                    'market_id' => $market->id,
                    'result' => $outcome->value,
                ],
                ['name' => $outcome->name(), 'sport' => LeagueSport::BASKETBALL]
            );
        }
    }

    private static function formatMarketName(string $name): string
    {
        return Str::of($name)->replace(['Home', 'Away'], ['{home}', '{away}']);
    }
}
