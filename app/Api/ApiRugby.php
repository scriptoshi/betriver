<?php

namespace App\Api;


use App\Enums\LeagueSport;
use App\Enums\Rugby\GameStatus;
use App\Enums\Rugby\ScoreType;
use App\Events\GameUpdated;
use App\Models\Game;
use App\Support\EventHydrant;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Support\Collection;

class ApiRugby extends ApiSports
{



    public static function url($url)
    {
        return "https://v1.rugby.api-sports.io/$url";
    }

    public static function sport(): LeagueSport
    {
        return LeagueSport::RUGBY;
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
     * Update live games
     * @param Collection $games
     * @return void
     */
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
            GameUpdated::dispatch(EventHydrant::hydrate($game));
        }
    }

    protected static function saveScores(Game $game, $lg)
    {
        $game->scores()->updateOrCreate([
            'type' => ScoreType::TOTAL
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
        $game->status = isset($lg->status)
            ? $lg->status?->short
            : $lg->game->status?->short;
        $game->elapsed = intval($lg->time);
        if (static::ended($game->status)) {
            $game->endTime = now();
            $game->closed = true;
        }
        $game->save();
    }
}
