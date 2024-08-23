<?php

namespace App\Api;

use App\Enums\Handball\GameStatus;
use App\Enums\Handball\ScoreType;
use App\Enums\LeagueSport;

use App\Models\Game;
use Illuminate\Support\Collection;
use Ixudra\Curl\Facades\Curl;

class ApiHandball extends ApiSports
{



    public static function url($url)
    {
        return "https://v1.handball.api-sports.io/$url";
    }

    public static function sport(): LeagueSport
    {
        return LeagueSport::HANDBALL;
    }

    public static function scoreTypes(): array
    {
        return ScoreType::cases();
    }

    public static function ended($status): bool
    {
        return GameStatus::from(strtoupper($status))->ended();
    }



    public static function updateLiveGame(Collection $games)
    {
        if ($games->count() > 1) $data = ['ids' => $games->map(fn($game) => $game->gameId)->implode('-')];
        else $data = ['id' => $games->first()->gameId];
        $response = Curl::to(static::url('games'))
            ->withHeader('x-apisports-key: ' . static::apiKey())
            ->asJsonResponse()
            ->withData($data)
            ->get();
        foreach ($response->response as $lg) {
            $game = Game::query()->where('gameId', $lg->game->id)->first();
            if (!$game) continue;
            static::saveScores($game, $lg);
        }
    }

    protected static function saveScores(Game $game, $lg)
    {
        $game->scores()->updateOrCreate([
            'type' => ScoreType::TOTAL->value,
        ], [
            'home' => $lg->scores->home,
            'away' => $lg->scores->away,
        ]);
        foreach ($lg->periods as $type => $score) {
            $game->scores()->updateOrCreate([
                'type' => $type,
            ], [
                'home' => $score->home,
                'away' => $score->away,
            ]);
        }
        $game->status = $lg->status->short;
        $game->elapsed = intval($lg->time);
        if (static::ended($lg->status->short)) {
            $game->endTime = now();
            $game->closed = true;
        }
        $game->save();
    }
}
