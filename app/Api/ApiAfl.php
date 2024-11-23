<?php

namespace App\Api;

use App\Enums\Afl\GameStatus;
use App\Enums\Afl\ScoreType;
use App\Enums\LeagueSport;
use App\Events\GameUpdated;
use App\Models\Game;
use App\Models\League;
use App\Models\Score;
use App\Models\Team;
use App\Support\EventHydrant;
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
        return GameStatus::tryFrom(strtoupper($status))?->ended() ?? true;
    }



    public static function url($url)
    {
        return "https://v1.afl.api-sports.io/$url";
    }



    /**
     * Update leagues
     * @return void
     */

    public static function updateLeagues()
    {
        $response = Curl::to(static::url('leagues'))
            ->withHeader('x-apisports-key: ' . static::apiKey())
            ->asJsonResponse()
            ->get();
        foreach ($response->response as $season) {
            if (Carbon::parse($season->end)->gt(now())) {
                League::query()->updateOrCreate([
                    'leagueId' => $season->id,
                    'sport' => static::sport(),
                ], [
                    'name' => $season->name,
                    'description' => $season->name,
                    'image' => $season->logo ?? null,
                    'country' => static::$country,
                    'season' => $season->season,
                    'season_ends_at' => Carbon::parse($season->end),
                ]);
            }
        }
    }


    protected static function saveScores(Game $game, $lg)
    {
        foreach (["score", "goals", "behinds", "psgoals", "psbehinds"] as $type) {
            $game->scores()->updateOrCreate(
                ['type' => ScoreType::fulltime($type)],
                ['home' => $lg->scores->home->$type, 'away' => $lg->scores->away->$type]
            );
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

    /**
     * Load Games
     * @return void
     */
    public static function loadGames($start, $days)
    {
        $games = parent::loadGames($start, $days);
        if (!count($games)) return;
        collect($games)->chunk(10)->each(fn($chunk) => static::updateQuaterScores($chunk));
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
        // safely update quatetrs
        $games->chunk(10)->each(fn($chunk) => static::updateQuaterScores($chunk));
    }

    public static function updateQuaterScores(Collection $games)
    {
        $gIds =  $games->pluck('id', 'gameId');
        if ($games->count() > 1) $data = ['ids' => $games->map(fn($game) => $game->gameId)->implode('-')];
        else $data = ['id' => $games->first()->gameId];
        $response = Curl::to(static::url('games/quarters'))
            ->withHeader('x-apisports-key: ' . static::apiKey())
            ->asJsonResponse()
            ->withData($data)
            ->get();
        foreach ($response->response as $game_result) {
            foreach ($game_result->quarters as $quarter) {
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
                            'game_id' => $gIds[(string)$game_result->game->id]
                        ],
                        ['home' => $scores->home->$type, 'away' => $scores->away->$type]
                    );
                }
            }
        }
    }
}
