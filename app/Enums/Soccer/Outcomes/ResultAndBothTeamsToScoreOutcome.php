<?php

namespace App\Enums\Soccer\Outcomes;


enum ResultAndBothTeamsToScoreOutcome: string
{
    case HOME_YES = 'home_yes';
    case HOME_NO = 'home_no';
    case DRAW_YES = 'draw_yes';
    case DRAW_NO = 'draw_no';
    case AWAY_YES = 'away_yes';
    case AWAY_NO = 'away_no';

    public function matchWinner(): MatchWinner
    {
        return match ($this) {
            self::HOME_YES, self::HOME_NO => MatchWinner::HOME,
            self::DRAW_YES, self::DRAW_NO => MatchWinner::DRAW,
            self::AWAY_YES, self::AWAY_NO => MatchWinner::AWAY,
        };
    }

    public function bothTeamsToScore(): YesNo
    {
        return match ($this) {
            self::HOME_YES, self::DRAW_YES, self::AWAY_YES => YesNo::YES,
            self::HOME_NO, self::DRAW_NO, self::AWAY_NO => YesNo::NO,
        };
    }

    public function name(): string
    {
        return formatName($this->matchWinner()->name() . ' & ' . $this->bothTeamsToScore()->name());
    }
}
