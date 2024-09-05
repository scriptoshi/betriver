<?php

namespace App\Api;

use App\Enums\Basketball\GameStatus;
use App\Enums\Basketball\ScoreType;
use App\Enums\LeagueSport;
use App\Models\Game;
use App\Models\League;
use Carbon\Carbon;
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
        return GameStatus::tryFrom(strtoupper($status))?->ended() ?? true;
    }


    public static function getBetOdds()
    {
        $response = Curl::to(static::url('bets'))
            ->withHeader('x-apisports-key: ' . static::apiKey())
            ->asJsonResponse()
            ->get();
        file_put_contents('bets.json', json_encode($response));
    }

    protected static function saveScores(Game $game, $lg)
    {

        foreach (static::scoreTypes() as $type) {
            $game->scores()->updateOrCreate([
                'type' => $type->value,
            ], [
                'home' => $type->getScore($lg->scores->home),
                'away' => $type->getScore($lg->scores->away),
            ]);
        }
        $game->status = isset($lg->status)
            ? $lg->status->short
            : $lg->game->status?->short;
        $game->elapsed = intval($lg->time);
        if (static::ended($game->status)) {
            $game->endTime = now();
            $game->closed = true;
        }
        $game->save();
    }
}
