<?php

namespace App\Enums\MMA\Markets;

use App\Contracts\BetMarket;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\Soccer\Outcomes\YesNo;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Market;
use App\Traits\MMAfights;
use Illuminate\Support\Str;

enum MMAFightDuration: string implements BetMarket
{
    use MMAfights;
    case GO_DISTANCE = 'go_distance';
    case START_ROUND_2 = 'start_round_2';
    case START_ROUND_3 = 'start_round_3';
    case START_ROUND_4 = 'start_round_4';
    case START_ROUND_5 = 'start_round_5';
    case END_FIRST_60_SECONDS = 'end_first_60_seconds';

    public function oddsId(): int
    {
        return match ($this) {
            self::GO_DISTANCE => 5,
            self::START_ROUND_2 => 7,
            self::START_ROUND_3 => 8,
            self::START_ROUND_4 => 9,
            self::START_ROUND_5 => 10,
            self::END_FIRST_60_SECONDS => 27,
        };
    }

    public function outcomes(): array
    {
        return YesNo::cases();
    }

    public function name(): string
    {
        return match ($this) {
            self::GO_DISTANCE => "Fight To Go the Distance",
            self::START_ROUND_2 => "Fight To Start Round 2",
            self::START_ROUND_3 => "Fight To Start Round 3",
            self::START_ROUND_4 => "Fight To Start Round 4",
            self::START_ROUND_5 => "Fight To Start Round 5",
            self::END_FIRST_60_SECONDS => "End in the 1st 60 Seconds",
        };
    }

    public function won(Game $fight, Bet $bet): bool
    {
        $outcome = YesNo::from($bet->result);
        $completedRounds = static::completedRounds($fight);
        $totalRounds = static::scheduledRounds($fight);
        $endTime = static::endTime($fight);
        return match ($this) {
            self::GO_DISTANCE => $completedRounds === $totalRounds && $outcome === YesNo::YES,
            self::START_ROUND_2 => $completedRounds >= 1 && $outcome === YesNo::YES,
            self::START_ROUND_3 => $completedRounds >= 2 && $outcome === YesNo::YES,
            self::START_ROUND_4 => $completedRounds >= 3 && $outcome === YesNo::YES,
            self::START_ROUND_5 => $completedRounds >= 4 && $outcome === YesNo::YES,
            self::END_FIRST_60_SECONDS => $endTime <= 60 && $outcome === YesNo::YES,
        };
    }

    public static function seed(): void
    {
        foreach (self::cases() as $case) {
            $market = Market::updateOrCreate(
                [
                    'segment' => $case->value,
                    'oddsId' => $case->oddsId(),
                    'sport' => LeagueSport::MMA
                ],
                [
                    'slug' => Str::slug($case->name()),
                    'description' => $case->name(),
                    'name' => $case->name(),
                    'type' => EnumsMarket::MMA_FIGHT_DURATION,
                ]
            );
            foreach (YesNo::cases() as $outcome) {
                Bet::updateOrCreate(
                    [
                        'market_id' => $market->id,
                        'name' => $outcome->name(),
                        'sport' => LeagueSport::MMA
                    ],
                    ['result' => $outcome->value]
                );
            }
        }
    }
}
