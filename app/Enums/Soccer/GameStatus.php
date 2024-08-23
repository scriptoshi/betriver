<?php

namespace App\Enums\Soccer;

use App\Contracts\GameStatus as ContractsGameStatus;

enum GameStatus: string implements ContractsGameStatus
{
    case TimeToBeDefined = 'TBD';
    case NotStarted = 'NS';
    case FirstHalf = '1H';
    case HalfTime = 'HT';
    case SecondHalf = '2H';
    case ExtraTime = 'ET';
    case BreakTime = 'BT';
    case PenaltyInProgress = 'P';
    case Suspended = 'SUSP';
    case Interrupted = 'INT';
    case Finished = 'FT';
    case FinishedAfterExtraTime = 'AET';
    case FinishedAfterPenalty = 'PEN';
    case Postponed = 'PST';
    case Cancelled = 'CANC';
    case Abandoned = 'ABD';
    case TechnicalLoss = 'AWD';
    case Walkover = 'WO';
    case InProgress = 'LIVE';

    public function description(): string
    {
        return match ($this) {
            self::TimeToBeDefined => 'Time To Be Defined',
            self::NotStarted => 'Not Started',
            self::FirstHalf => 'First Half, Kick Off',
            self::HalfTime => 'Halftime',
            self::SecondHalf => 'Second Half, 2nd Half Started',
            self::ExtraTime => 'Extra Time',
            self::BreakTime => 'Break Time',
            self::PenaltyInProgress => 'Penalty In Progress',
            self::Suspended => 'Match Suspended',
            self::Interrupted => 'Match Interrupted',
            self::Finished => 'Match Finished',
            self::FinishedAfterExtraTime => 'Match Finished After Extra Time',
            self::FinishedAfterPenalty => 'Match Finished After Penalties',
            self::Postponed => 'Match Postponed',
            self::Cancelled => 'Match Cancelled',
            self::Abandoned => 'Match Abandoned',
            self::TechnicalLoss => 'Technical Loss',
            self::Walkover => 'Walkover',
            self::InProgress => 'In Progress',
        };
    }

    public function gameState(): string
    {
        return match ($this) {
            self::TimeToBeDefined, self::NotStarted => 'scheduled',
            self::FirstHalf, self::HalfTime, self::SecondHalf, self::ExtraTime, self::BreakTime, self::PenaltyInProgress, self::Suspended, self::Interrupted, self::InProgress => 'in_play',
            self::Finished, self::FinishedAfterExtraTime, self::FinishedAfterPenalty => 'finished',
            self::Postponed => 'postponed',
            self::Cancelled => 'cancelled',
            self::Abandoned => 'abandoned',
            self::TechnicalLoss, self::Walkover => 'not_played',
        };
    }

    public function statusText(): string
    {
        return match ($this) {
            self::TimeToBeDefined => 'Pending',
            self::NotStarted => 'Not Started',
            self::FirstHalf => '1st Half',
            self::HalfTime => 'Half Time',
            self::SecondHalf => '2nd Half',
            self::ExtraTime => 'Extra Time',
            self::BreakTime => 'Break',
            self::PenaltyInProgress => 'Penalties',
            self::Suspended => 'Suspended',
            self::Interrupted => 'Interrupted',
            self::Finished => 'Finished',
            self::FinishedAfterExtraTime => 'Finished',
            self::FinishedAfterPenalty => 'Penalties',
            self::Postponed => 'Postponed',
            self::Cancelled => 'Cancelled',
            self::Abandoned => 'Abandoned',
            self::TechnicalLoss => 'Tech. Loss',
            self::Walkover => 'Walkover',
            self::InProgress => 'Live',
        };
    }

    public function ended(): bool
    {
        return match ($this) {
            self::Finished,
            self::FinishedAfterExtraTime,
            self::FinishedAfterPenalty,
            self::Postponed,
            self::Cancelled,
            self::Abandoned,
            self::TechnicalLoss,
            self::Walkover => true,
            default => false
        };
    }

    public function detailedDescription(): string
    {
        return match ($this) {
            self::TimeToBeDefined => 'Scheduled but date and time are not known',
            self::NotStarted => '',
            self::FirstHalf => 'First half in play',
            self::HalfTime => 'Halftime break',
            self::SecondHalf => 'Second half in play',
            self::ExtraTime => 'Extra time in play',
            self::BreakTime => 'Break during extra time',
            self::PenaltyInProgress => 'Penalty played after extra time',
            self::Suspended => 'Suspended by referee\'s decision, may be rescheduled another day',
            self::Interrupted => 'Interrupted by referee\'s decision, should resume in a few minutes',
            self::Finished => 'Finished in the regular time',
            self::FinishedAfterExtraTime => 'Finished after extra time without going to the penalty shootout',
            self::FinishedAfterPenalty => 'Finished after the penalty shootout',
            self::Postponed => 'Postponed to another day, once the new date and time is known the status will change to Not Started',
            self::Cancelled => 'Cancelled, match will not be played',
            self::Abandoned => 'Abandoned for various reasons (Bad Weather, Safety, Floodlights, Playing Staff Or Referees), Can be rescheduled or not, it depends on the competition',
            self::TechnicalLoss => '',
            self::Walkover => 'Victory by forfeit or absence of competitor',
            self::InProgress => 'Used in very rare cases. It indicates a fixture in progress but the data indicating the half-time or elapsed time are not available',
        };
    }

    public function finished(): bool
    {
        return match ($this) {
            self::Finished,
            self::FinishedAfterExtraTime,
            self::FinishedAfterPenalty,
            self::TechnicalLoss,
            self::Walkover => true,
            default => false
        };
    }

    public function cancelled(): bool
    {
        return match ($this) {
            self::Postponed,
            self::Cancelled,
            self::Abandoned => true,
            default => false
        };
    }
}
