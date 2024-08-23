<?php

namespace App\Enums\Nfl;

use App\Contracts\GameStatus as ContractsGameStatus;

enum GameStatus: string implements ContractsGameStatus
{
    case NotStarted = 'NS';
    case FirstQuarter = 'Q1';
    case SecondQuarter = 'Q2';
    case ThirdQuarter = 'Q3';
    case FourthQuarter = 'Q4';
    case OverTime = 'OT';
    case HalfTime = 'HT';
    case Finished = 'FT';
    case AfterOverTime = 'AOT';
    case Cancelled = 'CANC';
    case Postponed = 'PST';

    public function description(): string
    {
        return match ($this) {
            self::NotStarted => 'Not Started',
            self::FirstQuarter => 'First Quarter (In Play)',
            self::SecondQuarter => 'Second Quarter (In Play)',
            self::ThirdQuarter => 'Third Quarter (In Play)',
            self::FourthQuarter => 'Fourth Quarter (In Play)',
            self::OverTime => 'Overtime (In Play)',
            self::HalfTime => 'Halftime (In Play)',
            self::Finished => 'Finished (Game Finished)',
            self::AfterOverTime => 'After Over Time (Game Finished)',
            self::Cancelled => 'Cancelled (Game cancelled and not rescheduled)',
            self::Postponed => 'Postponed (Game postponed and waiting for a new game date)',
        };
    }

    public function gameState(): string
    {
        return match ($this) {
            self::NotStarted => 'scheduled',
            self::FirstQuarter, self::SecondQuarter, self::ThirdQuarter, self::FourthQuarter, self::OverTime, self::HalfTime => 'in_play',
            self::Finished, self::AfterOverTime => 'finished',
            self::Cancelled => 'cancelled',
            self::Postponed => 'postponed',
        };
    }

    public function statusText(): string
    {
        return match ($this) {
            self::NotStarted => 'Not Started',
            self::FirstQuarter => '1st Quarter',
            self::SecondQuarter => '2nd Quarter',
            self::ThirdQuarter => '3rd Quarter',
            self::FourthQuarter => '4th Quarter',
            self::OverTime => 'Overtime',
            self::HalfTime => 'Halftime',
            self::Finished => 'Finished',
            self::AfterOverTime => 'Ended in Overtime',
            self::Cancelled => 'Cancelled',
            self::Postponed => 'Postponed',
        };
    }

    public function ended(): bool
    {
        return match ($this) {
            self::Finished,
            self::AfterOverTime,
            self::Cancelled,
            self::Postponed => true,
            default => false
        };
    }

    public function finished(): bool
    {
        return match ($this) {
            self::Finished,
            self::AfterOverTime => true,
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
