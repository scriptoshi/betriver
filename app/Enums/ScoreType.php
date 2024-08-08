<?php

namespace App\Enums;

enum ScoreType: string
{
    case TOTAL = 'total';
    case HALFTIME = 'halftime';
    case SECONDHALF = 'second_half'; // generic must be calculated
    case EITHERHALF = 'either_half'; // generic must be calculated
    case BOTHHALVES = 'both_halves'; // generic must be calculated
    case FULLTIME = 'fulltime';
    case EXTRATIME = 'extratime';
    case PENALITY = 'penality';
    case QUATER_1 = 'quater_1';
    case QUATER_2 = 'quater_2';
    case QUATER_3 = 'quater_3';
    case QUATER_4 = 'quater_4';
    case OVERTIME = 'overtime';
    case HITS = 'hits';
    case ERRORS = 'errors';
    case INNINGS = 'innings';
    case SCORE = 'score';
    case GOALS = 'goals';
    case BEHINDS = 'behinds';
    case PSGOALS = 'psgoals';
    case PSBEHINDS = 'psbehinds';
}
