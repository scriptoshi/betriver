<?php

namespace App\Api;

use App\Enums\Baseball\GameStatus;
use App\Enums\Baseball\ScoreType;
use App\Enums\LeagueSport;
use App\Models\Game;
use App\Models\League;
use Carbon\Carbon;
use Ixudra\Curl\Facades\Curl;

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
        return GameStatus::tryFrom(strtoupper($status))?->ended() ?? true;
    }

    /**
     * Update leagues
     * @return void
     */

    public static function updateLeagues()
    {
        $response = Curl::to(static::url('leagues') . '')
            ->withHeader('x-apisports-key: ' . static::apiKey())
            ->asJsonResponse()
            ->get();
        foreach ($response->response as $lg) {
            $season = collect($lg->seasons)->first(fn($ssn) => $ssn->current);
            if (!$season) continue;
            League::query()->updateOrCreate([
                'leagueId' => $lg->id,
                'sport' => static::sport(),
            ], [
                'name' => $lg->name,
                'description' => $lg->name,
                'image' => $lg->logo ?? $lg->country->flag,
                'country' => $lg->country->code ?? $lg->country->name ?? static::$country,
                'season' =>  $season->season ?? now()->year(),
                'season_ends_at' =>  Carbon::parse($season->end),
            ]);
        }
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
