<?php

namespace App\Enums\Soccer;

enum ScoreType: string
{
    case TOTAL = 'total';
    case HALFTIME = 'halftime';
    case FULLTIME = 'fulltime';
    case EXTRATIME = 'extratime';
    case PENALITY = 'penality';

    public function name()
    {
        return match ($this) {
            static::HALFTIME => 'Half Time Goals',
            static::TOTAL => 'Total Goals',
            static::FULLTIME => 'Full Time Goals',
            static::EXTRATIME => 'Extratime Time Goals',
            static::PENALITY => 'Penality Goals',
        };
    }
}
