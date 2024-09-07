<?php

namespace App\Enums\Mma\Outcomes;

use Illuminate\Support\Str;

enum MMAVictoryMethodOutcome: string
{
    case YES = 'yes';
    case NO = 'no';
    case KO_TKO = 'ko_tko';
    case SUBMISSION = 'submission';
    case DECISION = 'decision';
    case HOME_KO_TKO = 'home_ko_tko';
    case HOME_SUBMISSION = 'home_submission';
    case HOME_DECISION = 'home_decision';
    case AWAY_KO_TKO = 'away_ko_tko';
    case AWAY_SUBMISSION = 'away_submission';
    case AWAY_DECISION = 'away_decision';
    case HOME_KO_TKO_ROUND_1 = 'home_ko_tko_round_1';
    case HOME_KO_TKO_ROUND_2 = 'home_ko_tko_round_2';
    case HOME_KO_TKO_ROUND_3 = 'home_ko_tko_round_3';
    case HOME_KO_TKO_ROUND_4 = 'home_ko_tko_round_4';
    case HOME_KO_TKO_ROUND_5 = 'home_ko_tko_round_5';
    case HOME_SUBMISSION_ROUND_1 = 'home_submission_round_1';
    case HOME_SUBMISSION_ROUND_2 = 'home_submission_round_2';
    case HOME_SUBMISSION_ROUND_3 = 'home_submission_round_3';
    case HOME_SUBMISSION_ROUND_4 = 'home_submission_round_4';
    case HOME_SUBMISSION_ROUND_5 = 'home_submission_round_5';
    case AWAY_KO_TKO_ROUND_1 = 'away_ko_tko_round_1';
    case AWAY_KO_TKO_ROUND_2 = 'away_ko_tko_round_2';
    case AWAY_KO_TKO_ROUND_3 = 'away_ko_tko_round_3';
    case AWAY_KO_TKO_ROUND_4 = 'away_ko_tko_round_4';
    case AWAY_KO_TKO_ROUND_5 = 'away_ko_tko_round_5';
    case AWAY_SUBMISSION_ROUND_1 = 'away_submission_round_1';
    case AWAY_SUBMISSION_ROUND_2 = 'away_submission_round_2';
    case AWAY_SUBMISSION_ROUND_3 = 'away_submission_round_3';
    case AWAY_SUBMISSION_ROUND_4 = 'away_submission_round_4';
    case AWAY_SUBMISSION_ROUND_5 = 'away_submission_round_5';
    case KO_TKO_SUBMISSION = 'ko_tko_submission';
    case KO_TKO_DECISION = 'ko_tko_decision';
    case SUBMISSION_DECISION = 'submission_decision';

    public function name(): string
    {
        return match ($this) {
            self::YES => 'Yes',
            self::NO => 'No',
            self::KO_TKO => 'KO/TKO',
            self::SUBMISSION => 'Submission',
            self::DECISION => 'Decision',
            self::HOME_KO_TKO => '{home} by KO/TKO',
            self::HOME_SUBMISSION => '{home} by Submission',
            self::HOME_DECISION => '{home} by Decision',
            self::AWAY_KO_TKO => '{away} by KO/TKO',
            self::AWAY_SUBMISSION => '{away} by Submission',
            self::AWAY_DECISION => '{away} by Decision',
            self::HOME_KO_TKO_ROUND_1, self::HOME_KO_TKO_ROUND_2, self::HOME_KO_TKO_ROUND_3,
            self::HOME_KO_TKO_ROUND_4, self::HOME_KO_TKO_ROUND_5 =>
            '{home} by KO/TKO in Round ' . $this->getRound(),
            self::HOME_SUBMISSION_ROUND_1, self::HOME_SUBMISSION_ROUND_2, self::HOME_SUBMISSION_ROUND_3,
            self::HOME_SUBMISSION_ROUND_4, self::HOME_SUBMISSION_ROUND_5 =>
            '{home} by Submission in Round ' . $this->getRound(),
            self::AWAY_KO_TKO_ROUND_1, self::AWAY_KO_TKO_ROUND_2, self::AWAY_KO_TKO_ROUND_3,
            self::AWAY_KO_TKO_ROUND_4, self::AWAY_KO_TKO_ROUND_5 =>
            '{away} by KO/TKO in Round ' . $this->getRound(),
            self::AWAY_SUBMISSION_ROUND_1, self::AWAY_SUBMISSION_ROUND_2, self::AWAY_SUBMISSION_ROUND_3,
            self::AWAY_SUBMISSION_ROUND_4, self::AWAY_SUBMISSION_ROUND_5 =>
            '{away} by Submission in Round ' . $this->getRound(),
            self::KO_TKO_SUBMISSION => 'KO/TKO or Submission',
            self::KO_TKO_DECISION => 'KO/TKO or Decision',
            self::SUBMISSION_DECISION => 'Submission or Decision',
        };
    }

    public function matchesMethod(string $method): bool
    {
        return match ($this) {
            self::KO_TKO => in_array($method, ['KO', 'TKO']),
            self::SUBMISSION => $method === 'Submission',
            self::DECISION => $method === 'Decision',
            default => false,
        };
    }

    public function matchesMethodAndWinner(string $method, string $winner): bool
    {
        return match ($this) {
            self::HOME_KO_TKO => $winner === 'first' && in_array($method, ['KO', 'TKO']),
            self::HOME_SUBMISSION => $winner === 'first' && $method === 'Submission',
            self::HOME_DECISION => $winner === 'first' && $method === 'Decision',
            self::AWAY_KO_TKO => $winner === 'second' && in_array($method, ['KO', 'TKO']),
            self::AWAY_SUBMISSION => $winner === 'second' && $method === 'Submission',
            self::AWAY_DECISION => $winner === 'second' && $method === 'Decision',
            default => false,
        };
    }

    public function matchesMethodWinnerAndRound(string $method, string $winner, int $round): bool
    {
        $outcomeRound = $this->getRound();
        $outcomeMethod = $this->getMethod();
        $outcomeWinner = $this->getWinner();

        return $outcomeRound === $round &&
            $outcomeWinner === $winner &&
            (($outcomeMethod === 'ko_tko' && in_array($method, ['KO', 'TKO'])) ||
                ($outcomeMethod === 'submission' && $method === 'Submission'));
    }

    public function matchesMethodDoubleChance(string $method): bool
    {
        return match ($this) {
            self::KO_TKO_SUBMISSION => in_array($method, ['KO', 'TKO', 'Submission']),
            self::KO_TKO_DECISION => in_array($method, ['KO', 'TKO', 'Decision']),
            self::SUBMISSION_DECISION => in_array($method, ['Submission', 'Decision']),
            default => false,
        };
    }

    private function getRound(): ?int
    {
        if (preg_match('/_round_(\d+)$/', $this->value, $matches)) {
            return (int)$matches[1];
        }
        return null;
    }

    private function getMethod(): ?string
    {
        if (Str::contains($this->value, 'ko_tko')) {
            return 'ko_tko';
        } elseif (Str::contains($this->value, 'submission')) {
            return 'submission';
        }
        return null;
    }

    private function getWinner(): ?string
    {
        if (Str::startsWith($this->value, 'home_')) {
            return 'first';
        } elseif (Str::startsWith($this->value, 'away_')) {
            return 'second';
        }
        return null;
    }
}
