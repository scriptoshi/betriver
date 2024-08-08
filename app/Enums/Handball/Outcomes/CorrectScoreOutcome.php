<?php

namespace App\Enums\Handball\Outcomes;

enum CorrectScoreOutcome: string
{
    case HOME_10_9 = '10-9';
    case HOME_11_10 = '11-10';
    case HOME_12_11 = '12-11';
    case HOME_13_12 = '13-12';
    case HOME_14_13 = '14-13';
    case HOME_15_14 = '15-14';
    case HOME_16_15 = '16-15';
    case HOME_17_16 = '17-16';
    case HOME_18_17 = '18-17';
    case HOME_19_18 = '19-18';
    case HOME_20_19 = '20-19';
    case AWAY_9_10 = '9-10';
    case AWAY_10_11 = '10-11';
    case AWAY_11_12 = '11-12';
    case AWAY_12_13 = '12-13';
    case AWAY_13_14 = '13-14';
    case AWAY_14_15 = '14-15';
    case AWAY_15_16 = '15-16';
    case AWAY_16_17 = '16-17';
    case AWAY_17_18 = '17-18';
    case AWAY_18_19 = '18-19';
    case AWAY_19_20 = '19-20';
    case ANY_OTHER_HOME_WIN = 'any_other_home_win';
    case ANY_OTHER_AWAY_WIN = 'any_other_away_win';

    public function name(): string
    {
        return match ($this) {
            self::ANY_OTHER_HOME_WIN => 'Any Other {home} Win',
            self::ANY_OTHER_AWAY_WIN => 'Any Other {away} Win',
            default => $this->value,
        };
    }

    public function homeGoals(): ?int
    {
        if (in_array($this, [self::ANY_OTHER_HOME_WIN, self::ANY_OTHER_AWAY_WIN])) {
            return null;
        }
        $scores = explode('-', $this->value);
        return (int) $scores[0];
    }

    public function awayGoals(): ?int
    {
        if (in_array($this, [self::ANY_OTHER_HOME_WIN, self::ANY_OTHER_AWAY_WIN])) {
            return null;
        }
        $scores = explode('-', $this->value);
        return (int) $scores[1];
    }
}
