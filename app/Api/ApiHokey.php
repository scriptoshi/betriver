<?php

namespace App\Api;

use App\Enums\Hockey\GameStatus;
use App\Enums\Hockey\ScoreType;
use App\Enums\LeagueSport;

use App\Models\Game;
use Illuminate\Support\Collection;
use Ixudra\Curl\Facades\Curl;

class ApiHokey extends ApiSports
{
    public static function sport(): LeagueSport
    {
        return LeagueSport::HOCKEY;
    }


    public static function url($url)
    {
        return "https://v1.hockey.api-sports.io/$url";
    }

    public static function scoreTypes(): array
    {
        return ScoreType::cases();
    }

    public static function ended($status): bool
    {
        return GameStatus::from($status)->ended();
    }



    public static function updateLiveGame(Collection $games)
    {
        if ($games->count() > 1) $data = ['ids' => $games->map(fn ($game) => $game->gameId)->implode('-')];
        else $data = ['id' => $games->first()->gameId];
        $response = Curl::to(static::url('games'))
            ->withHeader('x-apisports-key: ' . static::apiKey())
            ->asJsonResponse()
            ->withData($data)
            ->get();
        foreach ($response->response as $lg) {
            $game = Game::query()->where('gameId', $lg->game->id)->first();
            if (!$game) continue;
            $game->scores()->createOrUpdate([
                'type' => 'total',
            ], [
                'home' => $lg->scores->home ?? null,
                'away' => $lg->scores->away ?? null,
            ]);
            foreach ($lg->periods as $type => $score) {
                [$home, $away] = is_null($score) ? [null, null] : explode('_', $score) + [null, null];
                $game->scores()->createOrUpdate([
                    'type' => $type,
                ], [
                    'home' => $home,
                    'away' => $away,
                ]);
            }
            $game->status = isset($lg->status)
                ? $lg->status->short
                : $lg->game->status?->short;
            $game->elapsed = intval($lg->timer);
            $status = GameStatus::from($game->status);
            if ($status->ended()) {
                $game->endTime = now();
                $game->closed = true;
            }
            $game->save();
        }
    }
}
