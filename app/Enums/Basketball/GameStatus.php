<?php

namespace App\Enums\Basketball;

enum GameStatus: string
{
    case NotStarted = 'NS';
    case Quarter1 = 'Q1';
    case Quarter2 = 'Q2';
    case Quarter3 = 'Q3';
    case Quarter4 = 'Q4';
    case OverTime = 'OT';
    case BreakTime = 'BT';
    case Halftime = 'HT';
    case Finished = 'FT';
    case AfterOverTime = 'AOT';
    case Postponed = 'POST';
    case Cancelled = 'CANC';
    case Suspended = 'SUSP';
    case Awarded = 'AWD';
    case Abandoned = 'ABD';

    function description()
    {
        return match ($this) {
            GameStatus::NotStarted => 'Game Not Started',
            GameStatus::Quarter1 => 'Quarter 1 (In Play)',
            GameStatus::Quarter2 => 'Quarter 2 (In Play)',
            GameStatus::Quarter3 => 'Quarter 3 (In Play)',
            GameStatus::Quarter4 => 'Quarter 4 (In Play)',
            GameStatus::OverTime => 'Over Time (In Play)',
            GameStatus::BreakTime => 'Break Time (In Play)',
            GameStatus::Halftime => 'Halftime (In Play)',
            GameStatus::Finished => 'Game Finished',
            GameStatus::AfterOverTime => 'Game Finished After Over Time',
            GameStatus::Postponed => 'Game Postponed',
            GameStatus::Cancelled => 'Game Cancelled',
            GameStatus::Suspended => 'Game Suspended',
            GameStatus::Awarded => 'Game Awarded',
            GameStatus::Abandoned => 'Game Abandoned',
        };
    }

    function gameState()
    {
        return match ($this) {
            GameStatus::NotStarted => 'scheduled',
            GameStatus::Quarter1, GameStatus::Quarter2, GameStatus::Quarter3,
            GameStatus::Quarter4, GameStatus::OverTime, GameStatus::BreakTime,
            GameStatus::Halftime => 'in_play',
            GameStatus::Finished, GameStatus::AfterOverTime => 'finished',
            GameStatus::Postponed => 'postponed',
            GameStatus::Cancelled => 'cancelled',
            GameStatus::Suspended => 'suspended',
            GameStatus::Awarded => 'awarded',
            GameStatus::Abandoned => 'abandoned',
        };
    }

    function statusText()
    {
        return match ($this) {
            GameStatus::NotStarted => 'Not Started',
            GameStatus::Quarter1 => '1st Quarter',
            GameStatus::Quarter2 => '2nd Quarter',
            GameStatus::Quarter3 => '3rd Quarter',
            GameStatus::Quarter4 => '4th Quarter',
            GameStatus::OverTime => 'Overtime',
            GameStatus::BreakTime => 'Break',
            GameStatus::Halftime => 'Halftime',
            GameStatus::Finished => 'Finished',
            GameStatus::AfterOverTime => 'Finished After OT',
            GameStatus::Postponed => 'Postponed',
            GameStatus::Cancelled => 'Cancelled',
            GameStatus::Suspended => 'Suspended',
            GameStatus::Awarded => 'Awarded',
            GameStatus::Abandoned => 'Abandoned',
        };
    }

    public function ended(): bool
    {
        return match ($this) {
            GameStatus::Finished,
            GameStatus::AfterOverTime,
            GameStatus::Postponed,
            GameStatus::Cancelled,
            GameStatus::Suspended,
            GameStatus::Awarded,
            GameStatus::Abandoned => true,
            default => false
        };
    }
}
