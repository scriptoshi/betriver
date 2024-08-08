<?php

namespace App\Enums\Volleyball;

enum GameStatus: string
{
    case NotStarted = 'NS';
    case Set1 = 'S1';
    case Set2 = 'S2';
    case Set3 = 'S3';
    case Set4 = 'S4';
    case Set5 = 'S5';
    case Awarded = 'AW';
    case Postponed = 'POST';
    case Cancelled = 'CANC';
    case Interrupted = 'INTR';
    case Abandoned = 'ABD';
    case Finished = 'FT';

    public function description(): string
    {
        return match ($this) {
            self::NotStarted => 'Not Started',
            self::Set1 => 'Set 1 (In Play)',
            self::Set2 => 'Set 2 (In Play)',
            self::Set3 => 'Set 3 (In Play)',
            self::Set4 => 'Set 4 (In Play)',
            self::Set5 => 'Set 5 (In Play)',
            self::Awarded => 'Awarded',
            self::Postponed => 'Postponed',
            self::Cancelled => 'Cancelled',
            self::Interrupted => 'Interrupted',
            self::Abandoned => 'Abandoned',
            self::Finished => 'Finished (Game Finished)',
        };
    }

    public function gameState(): string
    {
        return match ($this) {
            self::NotStarted => 'scheduled',
            self::Set1, self::Set2, self::Set3, self::Set4, self::Set5 => 'in_play',
            self::Awarded, self::Finished => 'finished',
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
            self::Set1 => '1st Set',
            self::Set2 => '2nd Set',
            self::Set3 => '3rd Set',
            self::Set4 => '4th Set',
            self::Set5 => '5th Set',
            self::Awarded => 'Awarded',
            self::Postponed => 'Postponed',
            self::Cancelled => 'Cancelled',
            self::Interrupted => 'Interrupted',
            self::Abandoned => 'Abandoned',
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
            self::Finished => true,
            default => false
        };
    }
}
