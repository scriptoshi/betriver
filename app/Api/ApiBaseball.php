<?php

namespace App\Api;

use App\Enums\Baseball\GameStatus;
use App\Enums\Baseball\ScoreType;
use App\Enums\LeagueSport;

class ApiBaseball extends ApiSports
{

    public static function url($url)
    {
        return "https://v1.baseball.api-sports.io/$url";
    }


    public static function sport(): LeagueSport
    {
        return LeagueSport::BASEBALL;
    }

    public static function scoreTypes(): array
    {
        return ScoreType::cases();
    }

    public static function ended($status): bool
    {
        return GameStatus::from($status)->ended();
    }
}
