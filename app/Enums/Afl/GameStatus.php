<?php

namespace App\Enums\Afl;

use App\Contracts\GameStatus as ContractsGameStatus;

enum GameStatus: string implements ContractsGameStatus
{
    case NotStarted = 'NS';
    case FirstQuarter = 'Q1';
    case SecondQuarter = 'Q2';
    case ThirdQuarter = 'Q3';
    case FourthQuarter = 'Q4';
    case QuarterTime = 'QT';
    case EndOfRegulation = 'ER';
    case FullTime = 'FT';
    case HalfTime = 'HT';
    case Cancelled = 'CANC';
    case Postponed = 'PST';

    public function description(): string
    {
        return match ($this) {
            self::NotStarted => 'Not Started',
            self::FirstQuarter => '1st Quarter',
            self::SecondQuarter => '2nd Quarter',
            self::ThirdQuarter => '3rd Quarter',
            self::FourthQuarter => '4th Quarter',
            self::QuarterTime => 'Quarter Time',
            self::EndOfRegulation => 'End Of Regulation',
            self::FullTime => 'Full Time',
            self::HalfTime => 'Half Time',
            self::Cancelled => 'Cancelled (Game cancelled and not rescheduled)',
            self::Postponed => 'Postponed (Game postponed and waiting for a new Game date)',
        };
    }

    public function gameState(): string
    {
        return match ($this) {
            self::NotStarted => 'scheduled',
            self::FirstQuarter, self::SecondQuarter, self::ThirdQuarter, self::FourthQuarter,
            self::QuarterTime, self::HalfTime => 'in_play',
            self::EndOfRegulation, self::FullTime => 'finished',
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
            self::QuarterTime => 'Quarter Time',
            self::EndOfRegulation => 'End of Regulation',
            self::FullTime => 'Full Time',
            self::HalfTime => 'Half Time',
            self::Cancelled => 'Cancelled',
            self::Postponed => 'Postponed',
        };
    }

    public function ended(): bool
    {
        return match ($this) {
            self::Postponed,
            self::Cancelled,
            self::FullTime,
            self::EndOfRegulation => true,
            default => false
        };
    }

    public function cancelled(): bool
    {
        return match ($this) {
            self::Postponed,
            self::Cancelled => true,
            default => false
        };
    }

    public function finished(): bool
    {
        return match ($this) {
            self::FullTime,
            self::EndOfRegulation => true,
            default => false
        };
    }
}
