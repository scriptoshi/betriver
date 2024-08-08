<?php

namespace App\Enums;

enum GameStatus: string
{
    case Unknown = 'TBD';
    case NotStarted = 'NS';
    case FirstHalf = '1H';
    case Halftime = 'HT';
    case SecondHalf = '2H';
    case ExtraTime = 'ET';
    case BreakTime = 'BT';
    case Penalty = 'P';
    case Suspended = 'SUSP';
    case Interrupted = 'INT';
    case FinishedRegularTime = 'FT';
    case FinishedExtraTime = 'AET';
    case FinishedPenality = 'PEN';
    case Postponed = 'PST';
    case Cancelled = 'CANC';
    case Abandoned = 'ABD';
    case TechnicalLoss = 'AWD';
    case WalkOver = 'WO'; //Victory by forfeit or absence of competitor
    case InProgress = 'LIVE'; //In Play

    function description()
    {
        return match ($this) {
            GameStatus::Unknown => 'Scheduled but date and time are not known',
            GameStatus::NotStarted => 'Not Started Scheduled but pending',
            GameStatus::FirstHalf => 'First Half, Kick Off. First half currently in play',
            GameStatus::Halftime => 'Halftime Finished in the regular time',
            GameStatus::SecondHalf => 'Second Half, 2nd Half Started, currently in  play',
            GameStatus::ExtraTime => 'Extra time in play',
            GameStatus::BreakTime => 'Break during extra time',
            GameStatus::Penalty => 'Penaly played after extra time',
            GameStatus::Suspended => 'Match Suspended by referee\'s decision, may be rescheduled another day',
            GameStatus::Interrupted => 'Match Interrupted by referee\'s decision, should resume in a few minutes',
            GameStatus::FinishedRegularTime => 'Match Finished in the regular time',
            GameStatus::FinishedExtraTime => 'Match Finished after extra time without going to the penalty shootout',
            GameStatus::FinishedPenality => 'Match Finished after the penalty shootout',
            GameStatus::Postponed => 'Match Postponed to another day, once the new date and time is known the status will change to Not Started',
            GameStatus::Cancelled => 'Match Cancelled, match will not be played',
            GameStatus::Abandoned => 'Abandoned for various reasons (Bad Weather, Safety, Floodlights, Playing Staff Or Referees), Can be rescheduled or not, it depends on the competition',
            GameStatus::TechnicalLoss => 'Technical Loss. Not Played',
            GameStatus::WalkOver => 'Victory by forfeit or absence of competitor',
            GameStatus::InProgress => 'Fixture in progress but the data indicating the half-time or elapsed time are not available'
        };
    }

    function gameState()
    {
        return match ($this) {
            GameStatus::Unknown => 'scheduled',
            GameStatus::NotStarted => 'scheduled',
            GameStatus::FirstHalf => 'in_play',
            GameStatus::Halftime => 'finished',
            GameStatus::SecondHalf => 'in_play',
            GameStatus::ExtraTime => 'in_play',
            GameStatus::BreakTime => 'in_play',
            GameStatus::Penalty => 'in_play',
            GameStatus::Suspended => 'in_play',
            GameStatus::Interrupted => 'in_play',
            GameStatus::FinishedRegularTime => 'finished',
            GameStatus::FinishedExtraTime => 'finished',
            GameStatus::FinishedPenality => 'finished',
            GameStatus::Postponed => 'postponed',
            GameStatus::Cancelled => 'cancelled',
            GameStatus::Abandoned => 'abandoned',
            GameStatus::TechnicalLoss => 'not_played',
            GameStatus::WalkOver => 'not_played',
            GameStatus::InProgress => 'in_play'
        };
    }

    function statusText()
    {
        return match ($this) {
            GameStatus::Unknown => 'TBD',
            GameStatus::NotStarted => 'Not Started',
            GameStatus::FirstHalf => '1st Half',
            GameStatus::Halftime => 'Halftime',
            GameStatus::SecondHalf => '2nd Half',
            GameStatus::ExtraTime => 'Extra Time',
            GameStatus::BreakTime => 'Break Time',
            GameStatus::Penalty => 'Penalty',
            GameStatus::Suspended => 'Suspended',
            GameStatus::Interrupted => 'Interrupted',
            GameStatus::FinishedRegularTime => 'Ended Regular Time',
            GameStatus::FinishedExtraTime => 'Ended in Extra time',
            GameStatus::FinishedPenality => 'Ended in Penalities',
            GameStatus::Postponed => 'Postponed',
            GameStatus::Cancelled => 'Cancelled',
            GameStatus::Abandoned => 'Abandoned',
            GameStatus::TechnicalLoss => 'Technical Loss',
            GameStatus::WalkOver => 'Walk Over',
            GameStatus::InProgress => 'In Progress'
        };
    }
}
