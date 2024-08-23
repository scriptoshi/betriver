<?php

namespace App\Traits;

use App\Models\Game;

trait MMAfights
{

    /**
     * determine mma fight winner
     */
    public static function mmaFightWinner(Game $fight): string|null
    {
        if ($fight->win_team_id === $fight->home_team_id) return 'first';
        if ($fight->win_team_id === $fight->away_team_id) return 'second';
        return null;
    }
    /**
     * Determine how the mma match was won
     */
    public static function fightWinningMethod(Game $fight)
    {
        $result = $fight->result['won_type'];
        return match ($result) {
            "KO", "TKO", "DQ", "NC" => 'KO',
            'SUB', "TSUB" => 'Submission',
            default => 'Decision'
        };
    }

    /**
     * determine how many rounds a fight lasted
     */
    public static function completedRounds(Game $fight)
    {
        return $fight->result['round'];
    }


    /**
     * determine how many rounds a fight scheduled to last
     */
    public static function scheduledRounds(Game $fight)
    {
        return $fight->rounds ?? 5;
    }

    /**
     * determine the time the fight ended
     */
    public static function endTime(Game $fight): int
    {
        return static::convertToSeconds($fight->result['minute']);
    }

    /**
     * will convert minute 2:34 to seconds;
     */
    public static  function convertToSeconds($runningTime): int
    {
        [$minutes, $seconds] = explode(':', $runningTime) + [0, 0];
        return ($minutes * 60) + $seconds;
    }
}
