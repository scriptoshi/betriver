<?php

namespace App\Enums\Handball;

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
    case Walkover = 'WO';
    case AfterExtraTime = 'AET';
    case AfterPenalties = 'AP';
    case Finished = 'FT';

    public function description(): string
    {
        return match ($this) {
            self::NotStarted => 'Game Not Started',
            self::FirstHalf => 'First Half (In Play)',
            self::SecondHalf => 'Second Half (In Play)',
            self::HalfTime => 'Half Time (In Play)',
            self::ExtraTime => 'Extra Time (In Play)',
            self::BreakTime => 'Break Time (In Play)',
            self::PenaltiesTime => 'Penalties Time (In Play)',
            self::Awarded => 'Game Awarded',
            self::Postponed => 'Game Postponed',
            self::Cancelled => 'Game Cancelled',
            self::Interrupted => 'Game Interrupted',
            self::Abandoned => 'Game Abandoned',
            self::Walkover => 'Walkover',
            self::AfterExtraTime => 'Game Finished After Extra Time',
            self::AfterPenalties => 'Game Finished After Penalties',
            self::Finished => 'Game Finished',
        };
    }

    public function gameState(): string
    {
        return match ($this) {
            self::NotStarted => 'scheduled',
            self::FirstHalf, self::SecondHalf, self::HalfTime,
            self::ExtraTime, self::BreakTime, self::PenaltiesTime => 'in_play',
            self::Finished, self::AfterExtraTime, self::AfterPenalties => 'finished',
            self::Postponed => 'postponed',
            self::Cancelled => 'cancelled',
            self::Interrupted => 'interrupted',
            self::Abandoned => 'abandoned',
            self::Awarded => 'awarded',
            self::Walkover => 'walkover',
        };
    }

    public function statusText(): string
    {
        return match ($this) {
            self::NotStarted => 'Not Started',
            self::FirstHalf => '1st Half',
            self::SecondHalf => '2nd Half',
            self::HalfTime => 'Halftime',
            self::ExtraTime => 'Extra Time',
            self::BreakTime => 'Break',
            self::PenaltiesTime => 'Penalties',
            self::Awarded => 'Awarded',
            self::Postponed => 'Postponed',
            self::Cancelled => 'Cancelled',
            self::Interrupted => 'Interrupted',
            self::Abandoned => 'Abandoned',
            self::Walkover => 'Walkover',
            self::AfterExtraTime => 'Finished After ET',
            self::AfterPenalties => 'Finished After Penalties',
            self::Finished => 'Finished',
        };
    }

    public function ended(): bool
    {
        return match ($this) {
            self::Finished,
            self::AfterExtraTime,
            self::AfterPenalties,
            self::Awarded,
            self::Postponed,
            self::Cancelled,
            self::Interrupted,
            self::Abandoned,
            self::Walkover => true,
            default => false
        };
    }

    public function finished(): bool
    {
        return match ($this) {
            self::Finished,
            self::AfterExtraTime,
            self::AfterPenalties,
            self::Awarded,
            self::Walkover => true,
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
