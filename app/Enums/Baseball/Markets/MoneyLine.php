<?php

namespace App\Enums\Baseball\Markets;

use App\Contracts\BetMarket;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Baseball\Outcomes\MoneyLineOutcome;
use App\Enums\GoalCount;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use Illuminate\Support\Str;
use App\Enums\MarketCategory;

enum MoneyLine: string implements BetMarket
{
    case FIRST_THREE_INNINGS = 'first_three_innings';
    case FIRST_FIVE_INNINGS = 'first_five_innings';
    case FIRST_SEVEN_INNINGS = 'first_seven_innings';

    public function oddsId(): int
    {
        return match ($this) {
            self::FIRST_THREE_INNINGS => 34,
            self::FIRST_FIVE_INNINGS => 4,
            self::FIRST_SEVEN_INNINGS => 47,
        };
    }

    public function outcomes(): array
    {
        return MoneyLineOutcome::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::FIRST_THREE_INNINGS => "Money Line (1st 3 Innings)",
            self::FIRST_FIVE_INNINGS => "Money Line (1st 5 Innings)",
            self::FIRST_SEVEN_INNINGS => "Money Line (1st 7 Innings)",
        };
    }

    public function won(Game $game, Bet $bet): bool
    {
        $outcome = MoneyLineOutcome::from($bet->result);
        $homeScore = $this->getScore($game, GoalCount::HOME);
        $awayScore = $this->getScore($game, GoalCount::AWAY);

        return match ($outcome) {
            MoneyLineOutcome::HOME => $homeScore > $awayScore,
            MoneyLineOutcome::AWAY => $awayScore > $homeScore,
            MoneyLineOutcome::DRAW => $homeScore === $awayScore,
        };
    }

    private function getScore(Game $game, GoalCount $team): float|int
    {
        $innings = match ($this) {
            self::FIRST_THREE_INNINGS => ['innings_1', 'innings_2', 'innings_3'],
            self::FIRST_FIVE_INNINGS => ['innings_1', 'innings_2', 'innings_3', 'innings_4', 'innings_5'],
            self::FIRST_SEVEN_INNINGS => ['innings_1', 'innings_2', 'innings_3', 'innings_4', 'innings_5', 'innings_6', 'innings_7'],
        };
        return $game->getScores($innings, $team);
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'type' => EnumsMarket::BASEBALL_MONEY_LINE,
                    'sport' => LeagueSport::BASEBALL,
                ],
                [
                    'slug' => Str::slug($case->name()),
                    'description' => $case->name(),
                    'category' => MarketCategory::getCategory(self::class),
                    'name' => self::formatMarketName($case->name()),
                    'sport' => LeagueSport::BASEBALL,
                ]
            );

            foreach (MoneyLineOutcome::cases() as $outcome) {
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
