<?php

namespace App\Enums\Handball\Markets;

use App\Contracts\BetMarket;
use App\Enums\Handball\Outcomes\HalftimeFulltimeOutcome;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum HalftimeFulltime: string implements BetMarket
{
    case HALFTIME_FULLTIME = 'halftime_fulltime';

    public function oddsId(): int
    {
        return 8;
    }

    public function outcomes(): array
    {
        return HalftimeFulltimeOutcome::cases();
    }

    public function name(): string
    {
        return "HT/FT Double";
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = HalftimeFulltimeOutcome::from($bet->result);

        $halftimeHome = $game->getScores('firsthalf', 'home');
        $halftimeAway = $game->getScores('firsthalf', 'away');
        $fulltimeHome = $game->getScores('total', 'home');
        $fulltimeAway = $game->getScores('total', 'away');

        $halftimeResult = $this->getResult($halftimeHome, $halftimeAway);
        $fulltimeResult = $this->getResult($fulltimeHome, $fulltimeAway);

        return $outcome->halftimeResult() === $halftimeResult && $outcome->fulltimeResult() === $fulltimeResult;
    }

    private function getResult(int $homeScore, int $awayScore): string
    {
        if ($homeScore > $awayScore) {
            return 'home';
        } elseif ($awayScore > $homeScore) {
            return 'away';
        } else {
            return 'draw';
        }
    }

    public static function seed(): void
    {
        $market = Market::updateOrCreate(
            [
                'segment' => self::HALFTIME_FULLTIME->value,
                'oddsId' => self::HALFTIME_FULLTIME->oddsId(),
                'type' => EnumsMarket::HANDBALL_HALFTIME_FULLTIME,
                'sport' => LeagueSport::HANDBALL,
            ],
            [
                'slug' => Str::slug(LeagueSport::HANDBALL->value . '-' . self::HALFTIME_FULLTIME->name()),
                'description' => self::HALFTIME_FULLTIME->name(),
                'name' => self::HALFTIME_FULLTIME->name(),
            ]
        );

        foreach (HalftimeFulltimeOutcome::cases() as $outcome) {
            Bet::updateOrCreate(
                [
                    'market_id' => $market->id,
                    'name' => $outcome->name(),
                ],
                [
                    'result' => $outcome->value,
                    'sport' => LeagueSport::HANDBALL,
                ]
            );
        }
    }
}
