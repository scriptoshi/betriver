<?php

namespace App\Enums\Baseball;

enum GameStatus: string
{
    case NotStarted = 'NS';
    case Inning1 = 'IN1';
    case Inning2 = 'IN2';
    case Inning3 = 'IN3';
    case Inning4 = 'IN4';
    case Inning5 = 'IN5';
    case Inning6 = 'IN6';
    case Inning7 = 'IN7';
    case Inning8 = 'IN8';
    case Inning9 = 'IN9';
    case Postponed = 'POST';
    case Cancelled = 'CANC';
    case Interrupted = 'INTR';
    case Abandoned = 'ABD';
    case Finished = 'FT';

    function description()
    {
        return match ($this) {
            GameStatus::NotStarted => 'Not Started',
            GameStatus::Inning1 => 'Inning 1 (In Play)',
            GameStatus::Inning2 => 'Inning 2 (In Play)',
            GameStatus::Inning3 => 'Inning 3 (In Play)',
            GameStatus::Inning4 => 'Inning 4 (In Play)',
            GameStatus::Inning5 => 'Inning 5 (In Play)',
            GameStatus::Inning6 => 'Inning 6 (In Play)',
            GameStatus::Inning7 => 'Inning 7 (In Play)',
            GameStatus::Inning8 => 'Inning 8 (In Play)',
            GameStatus::Inning9 => 'Inning 9 (In Play)',
            GameStatus::Postponed => 'Game Postponed',
            GameStatus::Cancelled => 'Game Cancelled',
            GameStatus::Interrupted => 'Game Interrupted',
            GameStatus::Abandoned => 'Game Abandoned',
            GameStatus::Finished => 'Game Finished',
        };
    }

    function gameState()
    {
        return match ($this) {
            GameStatus::NotStarted => 'scheduled',
            GameStatus::Inning1, GameStatus::Inning2, GameStatus::Inning3,
            GameStatus::Inning4, GameStatus::Inning5, GameStatus::Inning6,
            GameStatus::Inning7, GameStatus::Inning8, GameStatus::Inning9 => 'in_play',
            GameStatus::Postponed => 'postponed',
            GameStatus::Cancelled => 'cancelled',
            GameStatus::Interrupted => 'interrupted',
            GameStatus::Abandoned => 'abandoned',
            GameStatus::Finished => 'finished',
        };
    }

    function statusText()
    {
        return match ($this) {
            GameStatus::NotStarted => 'Not Started',
            GameStatus::Inning1 => '1st Inning',
            GameStatus::Inning2 => '2nd Inning',
            GameStatus::Inning3 => '3rd Inning',
            GameStatus::Inning4 => '4th Inning',
            GameStatus::Inning5 => '5th Inning',
            GameStatus::Inning6 => '6th Inning',
            GameStatus::Inning7 => '7th Inning',
            GameStatus::Inning8 => '8th Inning',
            GameStatus::Inning9 => '9th Inning',
            GameStatus::Postponed => 'Postponed',
            GameStatus::Cancelled => 'Cancelled',
            GameStatus::Interrupted => 'Interrupted',
            GameStatus::Abandoned => 'Abandoned',
            GameStatus::Finished => 'Finished',
        };
    }

    function ended()
    {
        return match ($this) {
            GameStatus::Postponed,
            GameStatus::Cancelled,
            GameStatus::Interrupted,
            GameStatus::Abandoned,
            GameStatus::Finished => true,
            default => false,
        };
    }
}
