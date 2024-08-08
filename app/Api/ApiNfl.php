<?php

namespace App\Api;

use App\Enums\LeagueSport;
use App\Enums\Nfl\GameStatus;
use App\Enums\Nfl\ScoreType;

class ApiNfl extends ApiSports
{


    public static function url($url)
    {
        return "https://v1.american-football.api-sports.io/$url";
    }

    public static function apiKey()
    {
        return settings('site.apifootball_api_key');
    }

    public static function scoreTypes(): array
    {
        return ScoreType::cases();
    }

    public static function ended($status): bool
    {
        return GameStatus::from($status)->ended();
    }


    public static function sport(): LeagueSport
    {
        return LeagueSport::NFL;
    }
}
