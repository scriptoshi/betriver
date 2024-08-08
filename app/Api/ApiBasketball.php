<?php

namespace App\Api;

use App\Enums\Basketball\GameStatus;
use App\Enums\Basketball\ScoreType;
use App\Enums\LeagueSport;
use Ixudra\Curl\Facades\Curl;

class ApiBasketball extends ApiSports
{

    public static function url($url)
    {
        return "https://v1.basketball.api-sports.io/$url";
    }


    public static function sport(): LeagueSport
    {
        return LeagueSport::BASKETBALL;
    }

    public static function scoreTypes(): array
    {
        return ScoreType::cases();
    }

    public static function ended($status): bool
    {
        return GameStatus::from($status)->ended();
    }



    public static function getBetOdds()
    {
        $response = Curl::to(static::url('bets'))
            ->withHeader('x-apisports-key: ' . static::apiKey())
            ->asJsonResponse()
            ->get();
        file_put_contents('bets.json', json_encode($response));
    }
}
