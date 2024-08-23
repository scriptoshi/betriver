<?php

namespace App\Enums\Mma;

use App\Contracts\GameStatus as ContractsGameStatus;

enum GameStatus: string implements ContractsGameStatus
{
    case NotStarted = 'NS';
    case Intros = 'IN';
    case PreFight = 'PF';
    case InProgress = 'LIVE';
    case EndOfRound = 'EOR';
    case Finished = 'FT';
    case Walkouts = 'WO';
    case Cancelled = 'CANC';
    case Postponed = 'PST';

    public function description(): string
    {
        return match ($this) {
            self::NotStarted => 'Not Started',
            self::Intros => 'Introductions',
            self::PreFight => 'Pre-fight',
            self::InProgress => 'Fight In Progress',
            self::EndOfRound => 'End of Round',
            self::Finished => 'Fight Finished',
            self::Walkouts => 'Fighter Walkouts',
            self::Cancelled => 'Cancelled (Fight cancelled and not rescheduled)',
            self::Postponed => 'Postponed (Fight postponed and waiting for a new Fight date)',
        };
    }

    public function gameState(): string
    {
        return match ($this) {
            self::NotStarted, self::Walkouts => 'scheduled',
            self::Intros, self::PreFight, self::InProgress, self::EndOfRound => 'in_progress',
            self::Finished => 'finished',
            self::Cancelled => 'cancelled',
            self::Postponed => 'postponed',
        };
    }

    public function statusText(): string
    {
        return match ($this) {
            self::NotStarted => 'Not Started',
            self::Intros => 'Introductions',
            self::PreFight => 'Pre-fight',
            self::InProgress => 'Live',
            self::EndOfRound => 'End of Round',
            self::Finished => 'Finished',
            self::Walkouts => 'Walkouts',
            self::Cancelled => 'Cancelled',
            self::Postponed => 'Postponed',
        };
    }

    public function ended(): bool
    {
        return match ($this) {
            self::Finished,
            self::Cancelled,
            self::Walkouts,
            self::Postponed => true,
            default => false
        };
    }

    public function finished(): bool
    {
        return match ($this) {
            self::Finished,
            self::Walkouts => true,
            default => false
        };
    }

    public function cancelled(): bool
    {
        return match ($this) {
            self::Cancelled,
            self::Postponed => true,
            default => false
        };
    }
}
