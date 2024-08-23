<?php

namespace App\Enums\Hockey;

use App\Contracts\GameStatus as ContractsGameStatus;

enum GameStatus: string implements ContractsGameStatus
{
    case NotStarted = 'NS';
    case FirstPeriod = 'P1';
    case SecondPeriod = 'P2';
    case ThirdPeriod = 'P3';
    case OverTime = 'OT';
    case PenaltiesTime = 'PT';
    case BreakTime = 'BT';
    case Awarded = 'AW';
    case Postponed = 'POST';
    case Cancelled = 'CANC';
    case Interrupted = 'INTR';
    case Abandoned = 'ABD';
    case AfterOverTime = 'AOT';
    case AfterPenalties = 'AP';
    case Finished = 'FT';

    public function description(): string
    {
        return match ($this) {
            self::NotStarted => 'Not Started',
            self::FirstPeriod => 'First Period (In Play)',
            self::SecondPeriod => 'Second Period (In Play)',
            self::ThirdPeriod => 'Third Period (In Play)',
            self::OverTime => 'Over Time (In Play)',
            self::PenaltiesTime => 'Penalties Time (In Play)',
            self::BreakTime => 'Break Time (In Play)',
            self::Awarded => 'Awarded',
            self::Postponed => 'Postponed',
            self::Cancelled => 'Cancelled',
            self::Interrupted => 'Interrupted',
            self::Abandoned => 'Abandoned',
            self::AfterOverTime => 'After Over Time (Game Finished)',
            self::AfterPenalties => 'After Penalties (Game Finished)',
            self::Finished => 'Finished (Game Finished)',
        };
    }

    public function gameState(): string
    {
        return match ($this) {
            self::NotStarted => 'scheduled',
            self::FirstPeriod, self::SecondPeriod, self::ThirdPeriod, self::OverTime, self::PenaltiesTime, self::BreakTime => 'in_play',
            self::Awarded, self::AfterOverTime, self::AfterPenalties, self::Finished => 'finished',
            self::Postponed => 'postponed',
            self::Cancelled => 'cancelled',
            self::Interrupted => 'interrupted',
            self::Abandoned => 'abandoned',
        };
    }

    public function statusText(): string
    {
        return match ($this) {
            self::NotStarted => 'Not Started',
            self::FirstPeriod => '1st Period',
            self::SecondPeriod => '2nd Period',
            self::ThirdPeriod => '3rd Period',
            self::OverTime => 'Over Time',
            self::PenaltiesTime => 'Penalties',
            self::BreakTime => 'Break Time',
            self::Awarded => 'Awarded',
            self::Postponed => 'Postponed',
            self::Cancelled => 'Cancelled',
            self::Interrupted => 'Interrupted',
            self::Abandoned => 'Abandoned',
            self::AfterOverTime => 'Ended in Over Time',
            self::AfterPenalties => 'Ended in Penalties',
            self::Finished => 'Finished',
        };
    }

    public function ended(): bool
    {
        return match ($this) {
            self::Awarded,
            self::Postponed,
            self::Cancelled,
            self::Interrupted,
            self::Abandoned,
            self::AfterOverTime,
            self::AfterPenalties,
            self::Finished => true,
            default => false
        };
    }

    public function finished(): bool
    {
        return match ($this) {
            self::Awarded,
            self::AfterOverTime,
            self::AfterPenalties,
            self::Finished => true,
            default => false
        };
    }

    public function cancelled(): bool
    {
        return match ($this) {
            self::Postponed,
            self::Cancelled,
            self::Interrupted,
            self::Abandoned => true,
            default => false
        };
    }
}
