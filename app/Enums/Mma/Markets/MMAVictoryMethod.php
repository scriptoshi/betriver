<?php

namespace App\Enums\MMA\Markets;

use App\Contracts\BetMarket;
use App\Enums\LeagueSport;
use App\Enums\Market as EnumsMarket;
use App\Enums\MMA\Outcomes\MMAVictoryMethodOutcome;
use App\Models\Bet;
use App\Models\Fight;
use App\Models\Game;
use App\Models\Market;
use App\Traits\MMAfights;
use Illuminate\Support\Str;

enum MMAVictoryMethod: string implements BetMarket
{
    use MMAfights;
    case HOME_SUBMISSION = 'home_submission';
    case HOME_KO_TKO_DQ = 'home_ko_tko_dq';
    case AWAY_SUBMISSION = 'away_submission';
    case AWAY_KO_TKO_DQ = 'away_ko_tko_dq';
    case VICTORY_METHOD = 'victory_method';
    case VICTORY_METHOD_PLAYER = 'victory_method_player';
    case METHOD_AND_ROUND_PLAYER = 'method_and_round_player';
    case VICTORY_METHOD_DOUBLE_CHANCE = 'victory_method_double_chance';

    public function oddsId(): int
    {
        return match ($this) {
            self::HOME_SUBMISSION => 17,
            self::HOME_KO_TKO_DQ => 18,
            self::AWAY_SUBMISSION => 19,
            self::AWAY_KO_TKO_DQ => 20,
            self::VICTORY_METHOD => 29,
            self::VICTORY_METHOD_PLAYER => 25,
            self::METHOD_AND_ROUND_PLAYER => 26,
            self::VICTORY_METHOD_DOUBLE_CHANCE => 28,
        };
    }

    public function outcomes(): array
    {
        return match ($this) {
            self::HOME_SUBMISSION, self::HOME_KO_TKO_DQ, self::AWAY_SUBMISSION, self::AWAY_KO_TKO_DQ =>
            [MMAVictoryMethodOutcome::YES, MMAVictoryMethodOutcome::NO],
            self::VICTORY_METHOD => [
                MMAVictoryMethodOutcome::KO_TKO,
                MMAVictoryMethodOutcome::SUBMISSION,
                MMAVictoryMethodOutcome::DECISION
            ],
            self::VICTORY_METHOD_PLAYER => [
                MMAVictoryMethodOutcome::HOME_KO_TKO,
                MMAVictoryMethodOutcome::HOME_SUBMISSION,
                MMAVictoryMethodOutcome::HOME_DECISION,
                MMAVictoryMethodOutcome::AWAY_KO_TKO,
                MMAVictoryMethodOutcome::AWAY_SUBMISSION,
                MMAVictoryMethodOutcome::AWAY_DECISION
            ],
            self::METHOD_AND_ROUND_PLAYER => [
                MMAVictoryMethodOutcome::HOME_KO_TKO_ROUND_1,
                MMAVictoryMethodOutcome::HOME_KO_TKO_ROUND_2,
                MMAVictoryMethodOutcome::HOME_KO_TKO_ROUND_3,
                MMAVictoryMethodOutcome::HOME_KO_TKO_ROUND_4,
                MMAVictoryMethodOutcome::HOME_KO_TKO_ROUND_5,
                MMAVictoryMethodOutcome::HOME_SUBMISSION_ROUND_1,
                MMAVictoryMethodOutcome::HOME_SUBMISSION_ROUND_2,
                MMAVictoryMethodOutcome::HOME_SUBMISSION_ROUND_3,
                MMAVictoryMethodOutcome::HOME_SUBMISSION_ROUND_4,
                MMAVictoryMethodOutcome::HOME_SUBMISSION_ROUND_5,
                MMAVictoryMethodOutcome::AWAY_KO_TKO_ROUND_1,
                MMAVictoryMethodOutcome::AWAY_KO_TKO_ROUND_2,
                MMAVictoryMethodOutcome::AWAY_KO_TKO_ROUND_3,
                MMAVictoryMethodOutcome::AWAY_KO_TKO_ROUND_4,
                MMAVictoryMethodOutcome::AWAY_KO_TKO_ROUND_5,
                MMAVictoryMethodOutcome::AWAY_SUBMISSION_ROUND_1,
                MMAVictoryMethodOutcome::AWAY_SUBMISSION_ROUND_2,
                MMAVictoryMethodOutcome::AWAY_SUBMISSION_ROUND_3,
                MMAVictoryMethodOutcome::AWAY_SUBMISSION_ROUND_4,
                MMAVictoryMethodOutcome::AWAY_SUBMISSION_ROUND_5
            ],

            self::VICTORY_METHOD_DOUBLE_CHANCE => [
                MMAVictoryMethodOutcome::KO_TKO_SUBMISSION,
                MMAVictoryMethodOutcome::KO_TKO_DECISION,
                MMAVictoryMethodOutcome::SUBMISSION_DECISION
            ],
        };
    }

    public function name(): string
    {
        return match ($this) {
            self::HOME_SUBMISSION => "{home} Win by Submission",
            self::HOME_KO_TKO_DQ => "{home} Win by KO/TKO/DQ",
            self::AWAY_SUBMISSION => "{away} Win by Submission",
            self::AWAY_KO_TKO_DQ => "{away} Win by KO/TKO/DQ",
            self::VICTORY_METHOD => "Method of Victory",
            self::VICTORY_METHOD_PLAYER => "Method of Victory (Player)",
            self::METHOD_AND_ROUND_PLAYER => "Method and Round (Player)",
            self::VICTORY_METHOD_DOUBLE_CHANCE => "Method of Victory (Double Chance)",
        };
    }

    public function won(Game $fight, Bet $bet): bool
    {
        $outcome = MMAVictoryMethodOutcome::from($bet->result);
        $winner = static::mmaFightWinner($fight);
        $method = static::fightWinningMethod($fight);
        $round = static::completedRounds($fight);
        return match ($this) {
            self::HOME_SUBMISSION => $winner === 'first' && $method === 'Submission' && $outcome === MMAVictoryMethodOutcome::YES,
            self::HOME_KO_TKO_DQ => $winner === 'first' && in_array($method, ['KO', 'TKO', 'DQ']) && $outcome === MMAVictoryMethodOutcome::YES,
            self::AWAY_SUBMISSION => $winner === 'second' && $method === 'Submission' && $outcome === MMAVictoryMethodOutcome::YES,
            self::AWAY_KO_TKO_DQ => $winner === 'second' && in_array($method, ['KO', 'TKO', 'DQ']) && $outcome === MMAVictoryMethodOutcome::YES,
            self::VICTORY_METHOD => $outcome->matchesMethod($method),
            self::VICTORY_METHOD_PLAYER => $outcome->matchesMethodAndWinner($method, $winner),
            self::METHOD_AND_ROUND_PLAYER => $outcome->matchesMethodWinnerAndRound($method, $winner, $round),
            self::VICTORY_METHOD_DOUBLE_CHANCE => $outcome->matchesMethodDoubleChance($method),
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
                    'name' => self::formatMarketName($case->name()),
                    'type' => EnumsMarket::MMA_VICTORY_METHOD,
                ]
            );
            foreach ($case->outcomes() as $outcome) {
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

    private static function formatMarketName(string $name): string
    {
        return Str::of($name)->replace(['{home}', '{away}'], ['Home', 'Away']);
    }
}
