<?php

namespace App\Enums\Baseball\Markets;

use App\Contracts\BetMarket;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Baseball\Outcomes\TeamTotalsOutcome;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;
use App\Enums\MarketCategory;

enum TeamTotals: string implements BetMarket
{
    case HOME_TOTAL = 'home_total';
    case AWAY_TOTAL = 'away_total';
    case HOME_HITS = 'home_hits';
    case AWAY_HITS = 'away_hits';

    public function oddsId(): int
    {
        return match ($this) {
            self::HOME_TOTAL => 7,
            self::AWAY_TOTAL => 8,
            self::HOME_HITS => 61,
            self::AWAY_HITS => 60,
        };
    }

    public function outcomes(): array
    {
        return TeamTotalsOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::HOME_TOTAL => "Total - Home",
            self::AWAY_TOTAL => "Total - Away",
            self::HOME_HITS => "Home Total Hits",
            self::AWAY_HITS => "Away Total Hits",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = TeamTotalsOutcome::from($bet->result);
        $actualTotal = $this->getTotal($game);

        return match ($outcome->type()) {
            'over' => $actualTotal > $outcome->threshold(),
            'under' => $actualTotal < $outcome->threshold(),
        };
    }

    private function getTotal(Game $game): int
    {
        $team = Str::before($this->value, '_');
        $type = Str::after($this->value, '_');
        return $game->getScores($type, $team);
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' => EnumsMarket::BASEBALL_TEAM_TOTALS,
                    'sport' => LeagueSport::BASEBALL,
                ],
                [
                    'slug' => Str::slug(LeagueSport::BASEBALL->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'category' => MarketCategory::getCategory(self::class),
                    'name' => self::formatMarketName($case->name()),
                    'sport' => LeagueSport::BASEBALL,
                ]
            );

            foreach (TeamTotalsOutcome::cases() as $outcome) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'result' => $outcome->value,
                    ],
                    ['name' => $outcome->name(), 'sport' => LeagueSport::BASEBALL]
                );
            }
        }
    }

    private static function formatMarketName(string $name): string
    {
        return Str::of($name)->replace(['Home', 'Away'], ['{home}', '{away}']);
    }
}
