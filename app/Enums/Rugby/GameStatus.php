<?php

namespace App\Enums\Rugby;

use App\Contracts\GameStatus as ContractsGameStatus;

enum GameStatus: string implements ContractsGameStatus
{
    case NotStarted = 'NS';
    case FirstHalf = '1H';
    case SecondHalf = '2H';
    case HalfTime = 'HT';
    case ExtraTime = 'ET';
    case BreakTime = 'BT';
    case PenaltiesTime = 'PT';
    case Awarded = 'AW';
    case Postponed = 'POST';
    case Cancelled = 'CANC';
    case Interrupted = 'INTR';
    case Abandoned = 'ABD';
    case AfterExtraTime = 'AET';
    case Finished = 'FT';

    public function description(): string
    {
        return match ($this) {
            self::NotStarted => 'Not Started',
            self::FirstHalf => 'First Half (In Play)',
            self::SecondHalf => 'Second Half (In Play)',
            self::HalfTime => 'Half Time (In Play)',
            self::ExtraTime => 'Extra Time (In Play)',
            self::BreakTime => 'Break Time (In Play)',
            self::PenaltiesTime => 'Penalties Time (In Play)',
            self::Awarded => 'Awarded',
            self::Postponed => 'Postponed',
            self::Cancelled => 'Cancelled',
            self::Interrupted => 'Interrupted',
            self::Abandoned => 'Abandoned',
            self::AfterExtraTime => 'After Extra Time (Game Finished)',
            self::Finished => 'Finished (Game Finished)',
        };
    }

    public function gameState(): string
    {
        return match ($this) {
            self::NotStarted => 'scheduled',
            self::FirstHalf, self::SecondHalf, self::HalfTime, self::ExtraTime, self::BreakTime, self::PenaltiesTime => 'in_play',
            self::Awarded, self::AfterExtraTime, self::Finished => 'finished',
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
            self::FirstHalf => '1st Half',
            self::SecondHalf => '2nd Half',
            self::HalfTime => 'Half Time',
            self::ExtraTime => 'Extra Time',
            self::BreakTime => 'Break Time',
            self::PenaltiesTime => 'Penalties',
            self::Awarded => 'Awarded',
            self::Postponed => 'Postponed',
            self::Cancelled => 'Cancelled',
            self::Interrupted => 'Interrupted',
            self::Abandoned => 'Abandoned',
            self::AfterExtraTime => 'Ended in Extra Time',
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
            self::AfterExtraTime,
            self::Finished => true,
            default => false
        };
    }

    public function finished(): bool
    {
        return match ($this) {
            self::Awarded,
            self::AfterExtraTime,
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
