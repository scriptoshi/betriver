<?php

namespace App\Enums\Afl\Markets;

use App\Contracts\BetMarket;
use App\Enums\Afl\Outcomes\AFLWinningMarginOutcome;
use App\Enums\Afl\ScoreType;
use App\Enums\GoalCount;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;

enum AFLWinningMargin: string implements BetMarket
{
    case HOME_WINNING_MARGIN = 'home_winning_margin';
    case AWAY_WINNING_MARGIN = 'away_winning_margin';

    public function oddsId(): int
    {
        return match ($this) {
            self::HOME_WINNING_MARGIN => 52,
            self::AWAY_WINNING_MARGIN => 53,
        };
    }

    public function outcomes(): array
    {
        return AFLWinningMarginOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::HOME_WINNING_MARGIN => "{home} Winning Margin",
            self::AWAY_WINNING_MARGIN => "{away} Winning Margin",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = AFLWinningMarginOutcome::from($bet->result);

        $homeScore = $game->getScores(ScoreType::fulltime(), GoalCount::HOME);
        $awayScore = $game->getScores(ScoreType::fulltime(), GoalCount::AWAY);
        $margin = abs($homeScore - $awayScore);

        $winner = $homeScore > $awayScore ? 'home' : 'away';

        if ($winner !== $this->getTeam()) {
            return false;
        }

        return $margin >= $outcome->minMargin() && $margin <= $outcome->maxMargin();
    }

    private function getTeam(): string
    {
        return Str::before($this->value, '_');
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'sport' => LeagueSport::AFL
                ],
                [
                    'slug' => Str::slug(LeagueSport::AFL->value . '-' . $case->name()),
                    'description' => $case->name(),
                    'name' => $case->name(),
                    'sport' => LeagueSport::AFL,
                    'type' => EnumsMarket::AFL_WINNING_MARGIN
                ]
            );
            foreach (AFLWinningMarginOutcome::cases() as $outcome) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'name' => $outcome->name(),
                    ],
                    ['result' => $outcome->value, 'sport' => LeagueSport::AFL]
                );
            }
        }
    }
}
