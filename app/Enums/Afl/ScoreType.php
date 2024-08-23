<?php

namespace App\Enums\Afl;

enum ScoreType: string
{
    case TOTAL_SCORE = 'score';
    case TOTAL_GOALS = 'goals';
    case TOTAL_BEHINDS = 'behinds';
    case TOTAL_PSGOALS = 'psgoals';
    case TOTAL_PSBEHINDS = 'psbehinds';
    case Q1_SCORE = 'q1_score';
    case Q1_GOALS = 'q1_goals';
    case Q1_BEHINDS = 'q1_behinds';
    case Q1_PSGOALS = 'q1_psgoals';
    case Q1_PSBEHINDS = 'q1_psbehinds';
    case Q2_SCORE = 'q2_score';
    case Q2_GOALS = 'q2_goals';
    case Q2_BEHINDS = 'q2_behinds';
    case Q2_PSGOALS = 'q2_psgoals';
    case Q2_PSBEHINDS = 'q2_psbehinds';
    case Q3_SCORE = 'q3_score';
    case Q3_GOALS = 'q3_goals';
    case Q3_BEHINDS = 'q3_behinds';
    case Q3_PSGOALS = 'q3_psgoals';
    case Q3_PSBEHINDS = 'q3_psbehinds';
    case Q4_SCORE = 'q4_score';
    case Q4_GOALS = 'q4_goals';
    case Q4_BEHINDS = 'q4_behinds';
    case Q4_PSGOALS = 'q4_psgoals';
    case Q4_PSBEHINDS = 'q4_psbehinds';

    public function name()
    {
        return match ($this) {
            static::TOTAL_SCORE => 'Total Score',
            static::TOTAL_GOALS => 'Total Goals',
            static::TOTAL_BEHINDS => 'Total Behinds',
            static::TOTAL_PSGOALS => 'Total PS Goals',
            static::TOTAL_PSBEHINDS => 'Total PS Behinds',
            static::Q1_SCORE => 'First quarter score',
            static::Q1_GOALS => 'First quarter goals',
            static::Q1_BEHINDS => 'First quarter behinds',
            static::Q1_PSGOALS => 'First quarter psgoals',
            static::Q1_PSBEHINDS => 'First quarter psbehinds',
            static::Q2_SCORE => 'Second quarter score',
            static::Q2_GOALS => 'Second quarter goals',
            static::Q2_BEHINDS => 'Second quarter behinds',
            static::Q2_PSGOALS => 'Second quarter psgoals',
            static::Q2_PSBEHINDS => 'Second quarter psbehinds',
            static::Q3_SCORE => 'Third quarter score',
            static::Q3_GOALS => 'Third quarter goals',
            static::Q3_BEHINDS => 'Third quarter behinds',
            static::Q3_PSGOALS => 'Third quarter psgoals',
            static::Q3_PSBEHINDS => 'Third quarter psbehinds',
            static::Q4_SCORE => 'Fourth quarter score',
            static::Q4_GOALS => 'Fourth quarter goals',
            static::Q4_BEHINDS => 'Fourth quarter behinds',
            static::Q4_PSGOALS => 'Fourth quarter psgoals',
            static::Q4_PSBEHINDS => 'Fourth quarter psbehinds',
        };
    }

    public static function firsthalf($type = 'score'): array
    {
        return match ($type) {
            'score', 'points'  => [static::Q1_SCORE->value, static::Q2_SCORE->value],
            'goals' => [static::Q1_GOALS->value, static::Q2_GOALS->value],
            'behinds' => [static::Q1_BEHINDS->value, static::Q2_BEHINDS->value],
            'psgoals' => [static::Q1_PSGOALS->value, static::Q2_PSGOALS->value],
            'psbehinds' => [static::Q1_PSBEHINDS->value, static::Q2_PSBEHINDS->value],
        };
    }

    public static function secondhalf($type = 'score'): array
    {
        return match ($type) {
            'score', 'points'  => [static::Q3_SCORE->value, static::Q4_SCORE->value],
            'goals' => [static::Q3_GOALS->value, static::Q4_GOALS->value],
            'behinds' => [static::Q3_BEHINDS->value, static::Q4_BEHINDS->value],
            'psgoals' => [static::Q3_PSGOALS->value, static::Q4_PSGOALS->value],
            'psbehinds' => [static::Q3_PSBEHINDS->value, static::Q4_PSBEHINDS->value],
        };
    }

    public static function fulltime($type = 'score'): string
    {
        return match ($type) {
            'score', 'points'  => static::TOTAL_SCORE->value,
            'goals' => static::TOTAL_GOALS->value,
            'behinds' => static::TOTAL_BEHINDS->value,
            'psgoals' => static::TOTAL_PSGOALS->value,
            'psbehinds' => static::TOTAL_PSBEHINDS->value,
        };
    }

    public static function firstquarter($type = 'score'): string
    {
        return match ($type) {
            'score', 'points' => static::Q1_SCORE->value,
            'goals' => static::Q1_GOALS->value,
            'behinds' => static::Q1_BEHINDS->value,
            'psgoals' => static::Q1_PSGOALS->value,
            'psbehinds' => static::Q1_PSBEHINDS->value,
        };
    }

    public static function secondquarter($type = 'score'): string
    {
        return match ($type) {
            'score', 'points'  => static::Q2_SCORE->value,
            'goals' => static::Q2_GOALS->value,
            'behinds' => static::Q2_BEHINDS->value,
            'psgoals' => static::Q2_PSGOALS->value,
            'psbehinds' => static::Q2_PSBEHINDS->value,
        };
    }

    public static function thirdquarter($type = 'score'): string
    {
        return match ($type) {
            'score', 'points'  => static::Q3_SCORE->value,
            'goals' => static::Q3_GOALS->value,
            'behinds' => static::Q3_BEHINDS->value,
            'psgoals' => static::Q3_PSGOALS->value,
            'psbehinds' => static::Q3_PSBEHINDS->value,
        };
    }
    public static function fourthquarter($type = 'score'): string
    {
        return match ($type) {
            'score', 'points'  => static::Q4_SCORE->value,
            'goals' => static::Q4_GOALS->value,
            'behinds' => static::Q4_BEHINDS->value,
            'psgoals' => static::Q4_PSGOALS->value,
            'psbehinds' => static::Q4_PSBEHINDS->value,
        };
    }

    public  function getScore($score)
    {
        dd($score);
    }
}
