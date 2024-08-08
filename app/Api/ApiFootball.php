<?php

namespace App\Api;

use App\Enums\LeagueSport;

use App\Models\Game;
use App\Models\League;
use App\Models\Team;

use Carbon\Carbon;

use Illuminate\Support\Collection;
use Ixudra\Curl\Facades\Curl;
use Str;

class ApiFootball
{
    public static function apiKey()
    {
        return settings('site.apifootball_api_key');
    }

    public static function url($url)
    {
        return "https://v3.football.api-sports.io/$url";
    }



    /**
     * Update leagues
     * @return void
     */

    public static function updateLeagues()
    {
        $response = Curl::to(static::url('leagues') . '')
            ->withHeader('x-apisports-key: ' . static::apiKey())
            ->withData(['current' => true])
            ->asJsonResponse()
            ->get();
        foreach ($response->response as $lg) {
            League::query()->updateOrCreate([
                'leagueId' => $lg->league->id,
                'sport' => LeagueSport::FOOTBALL,
            ], [
                'name' => $lg->league->name,
                'description' => $lg->league->name,
                'image' => $lg->league->logo,
                'country' => $lg->country->code ?? null,
                'season' => $lg->seasons[0]?->year
            ]);
        }
    }

    /**
     * Update teams
     * @return void
     */
    //daily
    public static function updateTeams()
    {
        $leagues = League::where('active', true)->get();
        foreach ($leagues as $league) :
            $response = Curl::to(static::url('teams'))
                ->withHeader('x-apisports-key: ' . static::apiKey())
                ->withData(['league' => $league->leagueId, 'season' => $league->season])
                ->asJsonResponse()
                ->get();
            foreach ($response?->response as $lg) {
                Team::query()->updateOrCreate([
                    'teamId' => $lg->team->id,
                ], [
                    'name' => $lg->team->name,
                    'code' => $lg->team->code,
                    'country' => $lg->team->country,
                    'description' =>  $lg->team->name,
                    'sport' => LeagueSport::FOOTBALL,
                    'image' =>  $lg->team->name
                ]);
            }
        endforeach;
    }


    /**
     * Update games
     * @return void
     */
    public static function updateGames()
    {
        $leagues = League::query()
            ->whereNotNull('leagueId')
            ->where('sport', LeagueSport::FOOTBALL)
            ->where('active', true)
            ->get();
        $teams = Team::query()
            ->whereNotNull('teamId')
            ->where('sport', LeagueSport::FOOTBALL)
            ->pluck('id', 'teamId')
            ->all();
        foreach ($leagues as $league) :
            $response = Curl::to(static::url('fixtures'))
                ->withHeader('x-apisports-key: ' . static::apiKey())
                ->asJsonResponse()
                ->withData([
                    'league' => $league->leagueId,
                    'season' => $league->season,
                    'from' => now()->format('Y-m-d'),
                    'to' => now()->addDays(10)->format('Y-m-d')
                ])
                ->get();

            foreach ($response->response as $lg) {
                Game::query()->updateOrCreate([
                    'gameId' => $lg->fixture->id,
                ], [
                    'slug' => Str::slug("{$lg->fixture->id} {$lg->teams->away->name} vs {$lg->teams->home->name}-" . Carbon::parse($lg->fixture->date)->format('Y-m-d')),
                    'league_id' => $league->id,
                    'home_team_id' => $teams[$lg->teams->home->id],
                    'away_team_id' => $teams[$lg->teams->away->id],
                    'name' => "{$lg->teams->away->name} vs {$lg->teams->home->name}",
                    'startTime' => Carbon::parse($lg->fixture->date),
                    'status' => $lg->fixture->status->short,
                    'sport' => LeagueSport::FOOTBALL,
                    'closed' => false
                ]);
            }
        endforeach;
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
        $response = Curl::to(static::url('fixtures'))
            ->withHeader('x-apisports-key: ' . static::apiKey())
            ->asJsonResponse()
            ->withData($data)
            ->get();
        foreach ($response->response as $lg) {
            $game = Game::query()->where('gameId', $lg->fixture->id)->first();
            if (!$game) continue;
            foreach ($lg->score as $type => $score) {
                $game->scores()->createOrUpdate([
                    'type' => $type,
                ], [
                    'home' => $score->home,
                    'away' => $score->away,
                ]);
            }
            $game->status = $lg->fixture->status->short;
            $game->elapsed = intval($lg->fixture->status->elapsed);
            if ($lg->fixture->status->short == 'FT') {
                $game->endTime = now();
                $game->closed = true;
            }
            $game->save();
        }
    }



    public static function getBetOdds()
    {
        $response = Curl::to(static::url('odds/bets'))
            ->withHeader('x-apisports-key: ' . static::apiKey())
            ->asJsonResponse()
            ->get();
        file_put_contents('bets.json', json_encode($response));
        dd(json_encode($response));
    }

    public static function getOdds()
    {
        $response = Curl::to(static::url('odds'))
            ->withHeader('x-apisports-key: ' . static::apiKey())
            ->withData(['league' => 180])
            ->asJsonResponse()
            ->get();
        file_put_contents('odds.json', json_encode($response));
        dd(json_encode($response));
    }
}
