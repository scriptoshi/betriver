<?php

namespace App\Api;

use App\Enums\Afl\GameStatus;
use App\Enums\Afl\ScoreType;
use App\Enums\LeagueSport;

use App\Models\Game;
use App\Models\League;
use App\Models\Score;
use App\Models\Team;

use Carbon\Carbon;

use Illuminate\Support\Collection;
use Ixudra\Curl\Facades\Curl;
use Str;

class ApiAfl extends ApiSports
{
    /**
     * AFL
     * Australian football League
     */
    public static $country = 'AU';

    public static function sport(): LeagueSport
    {
        return LeagueSport::AFL;
    }

    public static function scoreTypes(): array
    {
        return ScoreType::cases();
    }

    public static function ended($status): bool
    {
        return GameStatus::from($status)->ended();
    }

    public static function apiKey()
    {
        return settings('site.apifootball_api_key');
    }

    public static function url($url)
    {
        return "https://v1.afl.api-sports.io/$url";
    }

    /**
     * Update live games
     * @param Collection $games
     * @return void
     */

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
            foreach ($lg->score as $scores) {
                foreach (["score", "goals", "behinds", "psgoals", "psbehinds"] as $type) {
                    $game->scores()->createOrUpdate(
                        ['type' => ScoreType::fulltime($type)],
                        ['home' => $scores->home->$type, 'away' => $scores->away->$type]
                    );
                }
            }
            $game->status = $lg->status->short;
            $game->elapsed = intval($lg->time);
            $status = GameStatus::from($lg->status->short);
            if ($status->ended()) {
                $game->endTime = now();
                $game->closed = true;
            }
            $game->save();
        }
        // safely update quatetrs
        $games->chunk(10)->each(fn ($chunk) => static::updateQuaterScores($chunk));
    }

    public static function updateQuaterScores(Collection $games)
    {
        $gIds =  $games->pluck('id', 'gameId');
        if ($games->count() > 1) $data = ['ids' => $games->map(fn ($game) => $game->gameId)->implode('-')];
        else $data = ['id' => $games->first()->gameId];
        $response = Curl::to(static::url('games'))
            ->withHeader('x-apisports-key: ' . static::apiKey())
            ->asJsonResponse()
            ->withData($data)
            ->get();
        foreach ($response->response->quarters as $quarter) {
            foreach (["points", "goals", "behinds"] as $type) {
                $period = match ($quarter->quarter) {
                    1 => ScoreType::firstquarter($type),
                    2 => ScoreType::secondquarter($type),
                    3 => ScoreType::thirdquarter($type),
                    4 => ScoreType::fourthquarter($type),
                };
                $scores = $quarter->teams;
                Score::query()->updateOrCreate(
                    [
                        'type' => $period,
                        'game_id' => $gIds[(string)$response->game->id]
                    ],
                    ['home' => $scores->home->$type, 'away' => $scores->away->$type]
                );
            }
        }
    }
}
