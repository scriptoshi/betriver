<?php

namespace App\Api;

use App\Enums\LeagueSport;
use App\Enums\Soccer\GameStatus;
use App\Enums\Soccer\ScoreType;
use App\Models\Game;
use App\Models\League;
use App\Models\Odd;
use App\Models\Team;
use App\Support\Country;
use Carbon\Carbon;

use Illuminate\Support\Collection;
use Ixudra\Curl\Facades\Curl;
use Str;

class ApiFootball extends ApiSports
{

    public static function url($url)
    {
        return "https://v3.football.api-sports.io/$url";
    }


    public static function sport(): LeagueSport
    {
        return LeagueSport::FOOTBALL;
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
            ->withData(['current' => 'true'])
            ->asJsonResponse()
            ->get();
        foreach ($response->response as $lg) {
            $season = $lg->seasons[0];
            if (! $season || Carbon::parse($season->end)->lt(now())) continue;
            League::query()->updateOrCreate([
                'leagueId' => $lg->league->id,
                'sport' => LeagueSport::FOOTBALL,
            ], [
                'name' => $lg->league->name,
                'description' => $lg->league->name,
                'image' => $lg->league->logo,
                'country' => $lg->country->code ?? $lg->country->name ?? null,
                'season' => $season->year,
                'has_odds' => $season->coverage->odds ?? false,
                'season_ends_at' => Carbon::parse($season->end)
            ]);
        }
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
        $response = Curl::to(static::url('fixtures'))
            ->withHeader('x-apisports-key: ' . static::apiKey())
            ->asJsonResponse()
            ->withData($data)
            ->get();
        foreach ($response->response as $lg) {
            $game = Game::query()->where('gameId', $lg->fixture->id)->first();
            if (!$game) continue;
            static::saveScores($game, $lg);
        }
    }

    /**
     * Load Games
     * @return void
     */
    public static function loadGames($start, $days)
    {
        $leagues = League::query()
            ->whereNotNull('leagueId')
            ->where('sport', static::sport())
            ->where('active', true)
            ->pluck('id', 'leagueId')
            ->all();
        $teams = Team::query()
            ->whereNotNull('teamId')
            ->where('sport', static::sport())
            ->where('active', true)
            ->pluck('id', 'teamId')
            ->all();
        for ($i = 0; $i < ($days + 1); $i++) {
            $date = now()->addDays($i + $start)->format('Y-m-d');
            $response = Curl::to(static::url('fixtures'))
                ->withHeader('x-apisports-key: ' . static::apiKey())
                ->asJsonResponse()
                ->withData(['date' => $date])
                ->get();
            // find missing teams
            foreach ($response->response as $lg) {
                $leagueId = $leagues[$lg->league->id] ?? static::saveLeague($lg);
                $homeTeamId = $teams[$lg->teams->home->id] ?? static::saveTeam($lg, $lg->teams->home);
                $awayTeamId = $teams[$lg->teams->away->id] ?? static::saveTeam($lg, $lg->teams->away);
                $gameId = $lg->id ?? $lg->fixture->id;
                $game = Game::query()->updateOrCreate([
                    'gameId' => $gameId
                ], [
                    'slug' => Str::slug("{$gameId} {$lg->teams->home->name} vs {$lg->teams->away->name}-" . Carbon::parse($lg->fixture->date)->format('Y-m-d')),
                    'league_id' => $leagueId,
                    'home_team_id' => $homeTeamId,
                    'away_team_id' => $awayTeamId,
                    'name' => "{$lg->teams->home->name} vs {$lg->teams->away->name}",
                    'startTime' => Carbon::parse($lg->fixture->date, timezone: $lg->fixture->timezone ?? 'UTC'),
                    'status' => $lg->fixture->status->short,
                    'sport' => static::sport(),
                    'closed' => false
                ]);
                if (static::ended($game->status))
                    static::saveScores($game, $lg);
            }
        }
    }


    public static function saveTeam($game, $resTeam)
    {
        $team = Team::query()->updateOrCreate([
            'teamId' => $resTeam->id,
        ], [
            'name' => $resTeam->name,
            'code' => $resTeam->code ?? str($resTeam->name)->camel(),
            'country' => Country::get($game->league->country) ??  static::$country,
            'description' =>  $resTeam->name,
            'sport' => static::sport(),
            'image' =>  $resTeam->logo
        ]);
        return $team->id;
    }


    public static function saveLeague($game): int
    {
        $league = $game->league;
        $season = $league->season;
        $seasonEnds = Carbon::parse((string)$season)->endOfYear();
        $lg =  League::query()->updateOrCreate([
            'leagueId' => $league->id,
            'sport' => static::sport(),
        ], [
            'name' => $league->name,
            'description' => $league->type ?? $league->name,
            'image' => $league->logo ?? null,
            'country' => Country::get($league->country)  ?? static::$country,
            'season' => $season,
            'season_ends_at' => $seasonEnds,
        ]);
        return $lg->id;
    }

    public static function loadOdds(League $league)
    {
        $response = Curl::to(static::url('odds'))
            ->withHeader('x-apisports-key: ' . static::apiKey())
            ->withData([
                'league' => $league->leagueId,
                'season' => $league->season,
            ])
            ->asJsonResponse()
            ->get();

        $games = $league->games()->pluck('id', 'gameId')->all();
        $upserts = [];
        if ($response->results == 0) return back()->with('error', __('Api returned 0 results. No odds provider found'));
        $fxids = collect($response->response)->map(fn($odds) => $odds->fixture->id)->all();

        $missing = array_diff($fxids, array_keys($games));

        if (count($missing)) {
            static::loadOddsFixture($league, $missing);
            $games = $league->games()->pluck('id', 'gameId')->all();
        }
        foreach ($response->response as $odds) {
            $game = $games[$odds->fixture->id] ?? null;
            if (!$game) continue;
            foreach ($odds->bookmakers as $bookie) {
                foreach ($bookie->bets as $odd) {
                    $upsert = static::getUpserts($game, $league->id, $odd, $bookie->name);
                    $upserts =  [...$upserts, ...$upsert];
                }
            }
        }
        $updates = collect($upserts)->unique('md5')->all();
        Odd::upsert($updates, uniqueBy: ['md5'], update: ['odd']);
        return back()->with('success',  __(':count Odds were loaded for the league', ['count' => count($upserts)]));
    }

    /**
     * Load Games
     * @return void
     */
    private static function loadOddsFixture(League $league, array $fxid)
    {
        if (count($fxid) > 1) $data = ['ids' => collect($fxid)->implode('-')];
        else $data = ['id' => $fxid[0]];
        $teams = Team::query()
            ->whereNotNull('teamId')
            ->where('sport', static::sport())
            ->where('active', true)
            ->pluck('id', 'teamId')
            ->all();
        $response = Curl::to(static::url('fixtures'))
            ->withHeader('x-apisports-key: ' . static::apiKey())
            ->asJsonResponse()
            ->withData($data)
            ->get();
        // find missing teams
        foreach ($response->response as $lg) {
            $homeTeamId = $teams[$lg->teams->home->id] ?? static::saveTeam($lg, $lg->teams->home);
            $awayTeamId = $teams[$lg->teams->away->id] ?? static::saveTeam($lg, $lg->teams->away);
            $gameId = $lg->id ?? $lg->fixture->id;
            Game::query()->updateOrCreate([
                'gameId' => $gameId
            ], [
                'slug' => Str::slug("{$gameId} {$lg->teams->home->name} vs {$lg->teams->away->name}-" . Carbon::parse($lg->fixture->date)->format('Y-m-d')),
                'league_id' => $league->id,
                'home_team_id' => $homeTeamId,
                'away_team_id' => $awayTeamId,
                'name' => "{$lg->teams->home->name} vs {$lg->teams->away->name}",
                'startTime' => Carbon::parse($lg->fixture->date, timezone: $lg->fixture->timezone ?? 'UTC'),
                'status' => $lg->fixture->status->short,
                'sport' => static::sport(),
                'closed' => false
            ]);
        }
    }

    protected static function saveScores(Game $game, $info)
    {
        $game->status = $info->fixture->status->short;
        $game->elapsed = intval($info->fixture->status->elapsed);
        if (static::ended($game->status)) {
            $game->endTime = now();
            $game->closed = true;
        }
        if (GameStatus::from(strtoupper($info->fixture->status->short)) == GameStatus::Postponed) {
            $game->save();
            return;
        }
        foreach ($info->score as $type => $score) {
            $game->scores()->updateOrCreate([
                'type' => $type,
            ], [
                'home' => $score->home,
                'away' => $score->away,
            ]);
        }
        /**
         * current total
         */
        $game->scores()->updateOrCreate([
            'type' => ScoreType::TOTAL,
        ], [
            'home' => $info->goals->home,
            'away' => $info->goals->away,
        ]);
        if (($info->teams->home->winner ?? null))
            $game->win_team_id = $game->home_team_id;
        if (($info->teams->away->winner ?? null))
            $game->win_team_id = $game->away_team_id;
        $game->save();
    }
}
