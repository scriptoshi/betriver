<?php

namespace App\Api;

use InvalidArgumentException;
use App\Enums\LeagueSport;
use App\Models\Bet;
use App\Models\Game;
use App\Models\League;
use App\Models\Market;
use App\Models\Odd;
use App\Models\Team;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Ixudra\Curl\Facades\Curl;
use Str;

abstract class ApiSports
{
    /**
     * Country based league Eg AFL or Premiersgip
     */
    public static $country = null;

    public static function apiKey()
    {
        return config('services.apifootball.apikey', settings('site.apifootball_api_key'));
    }

    abstract  public static function url($url);
    abstract  public static function sport(): LeagueSport;
    abstract public static function scoreTypes(): array;
    abstract public static function ended($status): bool;



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

        foreach ($response->response as $league) {
            foreach ($league->seasons as $season) {
                if (Carbon::parse($season->end)->gt(now())) {
                    $league = $league->league ?? $league;
                    League::query()->updateOrCreate([
                        'leagueId' => $league->id,
                        'sport' => static::sport(),
                    ], [
                        'name' => $league->name,
                        'description' => $league->type ?? $league->name,
                        'image' => $league->logo ?? null,
                        'country' => $league->country->code ?? $league->country->name ?? static::$country,
                        'season' => $season->season ?? $season->year,
                        'season_ends_at' => Carbon::parse($season->end),
                    ]);
                    continue 2;
                }
            }
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
        $fxids = collect($response->response)->map(fn($odds) => $odds->game->id)->all();
        $missing = array_diff($fxids, array_keys($games));
        if (count($missing)) {
            static::loadOddsGame($league, $missing);
            $games = $league->games()->pluck('id', 'gameId')->all();
        }
        foreach ($response->response as $odds) {
            $game = $games[$odds->game->id] ?? null;
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

    public static function getUpserts($gameId, $leagueId, $odd, $bookie = null): array
    {
        $all = collect($odd->values)->map(fn($o) => static::betName($o->value))->all();
        $odds = collect($odd->values)
            ->flatMap(fn($o) => [static::betName($o->value) => $o->odd])
            ->all();
        $market = Market::query()
            ->with(['bets' => fn($q) => $q->whereIn('result', $all)])
            ->where('oddsId', $odd->id)->first();
        if (!$market) return [];
        return $market->bets->map(function (Bet $bet) use ($odds, $bookie, $gameId, $leagueId) {
            $mclass =  (new Game)->getMorphClass();
            return [
                'bet_id' =>  $bet->id,
                'market_id' =>  $bet->market_id,
                'league_id' =>  $leagueId,
                'game_id' => $gameId,
                'bookie' => $bookie,
                'game_type' => $mclass,
                'md5' => md5($bet->market_id . '-' . $bet->id . '-' . $gameId . '-' . $mclass),
                'odd' => $odds[$bet->result],
            ];
        })->all();
    }

    public static function betName($name): string
    {
        return str($name)
            ->replace('/', '_')
            ->replace(' ', '_')
            ->replace(':', '-')
            ->lower()
            ->value();
    }


    static function  runningTimeToSeconds(string $runningTime): int
    {

        [$hours, $minutes] = explode(':', $runningTime) + [0, 0];
        if ($hours < 0 || $minutes < 0 || $minutes >= 60) {
            throw new InvalidArgumentException("Invalid time values. Hours should be non-negative, and minutes should be between 0 and 59.");
        }
        $totalSeconds = ($hours * 3600) + ($minutes * 60);
        return $totalSeconds;
    }


    public static function getBetOdds()
    {
        $response = Curl::to(static::url('odds/bets'))
            ->withHeader('x-apisports-key: ' . static::apiKey())
            ->asJsonResponse()
            ->get();
        file_put_contents('bets.json', json_encode($response));
    }

    /**
     * Load Games
     * @return void|array
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
        $games = [];

        for ($i = 0; $i < ($days + 1); $i++) {
            $fromNow = $i + $start;
            $date = $fromNow == 0
                ? now()->format('Y-m-d')
                : ($fromNow < 0
                    ? now()->subDays($fromNow)->format('Y-m-d')
                    : now()->addDays($fromNow)->format('Y-m-d'));
            $response = Curl::to(static::url('games'))
                ->withHeader('x-apisports-key: ' . static::apiKey())
                ->asJsonResponse()
                ->withData(['date' => $date])
                ->get();
            foreach ($response->response as $lg) {
                $leagueId = $leagues[$lg->league->id] ?? static::saveLeague($lg);
                $homeTeamId = $teams[$lg->teams->home->id] ?? static::saveTeam($lg, $lg->teams->home);
                $awayTeamId = $teams[$lg->teams->away->id] ?? static::saveTeam($lg, $lg->teams->away);
                $leagues[$lg->league->id] = $leagueId;
                $teams[$lg->teams->home->id] = $homeTeamId;
                $teams[$lg->teams->away->id] = $awayTeamId;
                $gameId = $lg->id ?? $lg->game->id;
                $game = Game::query()->updateOrCreate([
                    'gameId' => $gameId
                ], [
                    'slug' => Str::slug("{$gameId} {$lg->teams->home->name} vs {$lg->teams->away->name}-" . Carbon::parse($lg->date, timezone: $lg->timezone ?? 'UTC')->format('Y-m-d')),
                    'league_id' => $leagueId,
                    'home_team_id' => $homeTeamId,
                    'away_team_id' => $awayTeamId,
                    'name' => "{$lg->teams->home->name} vs {$lg->teams->away->name}",
                    'startTime' => Carbon::parse($lg->date, timezone: $lg->timezone ?? 'UTC'),
                    'status' => $lg->status->short,
                    'sport' => static::sport(),
                    'closed' => false
                ]);
                if (static::ended($game->status)) {
                    static::saveScores($game, $lg);
                    $games[] = $game;
                }
            }
        }
        return $games;
    }


    public static function saveTeam($game, $resTeam)
    {
        $team = Team::query()->updateOrCreate([
            'teamId' => $resTeam->id,
        ], [
            'name' => $resTeam->name,
            'code' => $resTeam->code ?? str($resTeam->name)->camel(),
            'country' => $game->country->code ??  static::$country,
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
        $seasonEnds = value(function () use ($season) {
            if (str($season)->contains('-')) {
                $year = str($season)->after('-')->value();
                return Carbon::parse($year)->endOfYear();
            }
            return Carbon::parse((string)$season)->endOfYear();
        });
        if (!isset($league->name)) {
            $response = Curl::to(static::url('leagues'))
                ->withHeader('x-apisports-key: ' . static::apiKey())
                ->asJsonResponse()
                ->withData(['id' => $league->id, 'season' => $league->season])
                ->get();
            $league =   $response->response[0];
            if (isset($response->seasons[0]->end))
                $seasonEnds = Carbon::parse($response->seasons[0]->end);
            if (isset($league->end))
                $seasonEnds = Carbon::parse($league->end);
        }
        $lg =  League::query()->updateOrCreate([
            'leagueId' => $league->id,
            'sport' => static::sport(),
        ], [
            'name' => $league->name,
            'description' => $league->type ?? $league->name,
            'image' => $league->logo ?? null,
            'country' => $game->country->code ?? static::$country,
            'season' => $season,
            'season_ends_at' => $seasonEnds,
        ]);
        return  $lg->id;
    }

    /**
     * Load Games
     * @return void
     */
    protected static function loadOddsGame(League $league, array $fxid)
    {
        // if (count($fxid) > 1) $data = ['ids' => collect($fxid)->implode('-')];
        // else $data = ['id' => $fxid[0]];
        $teams = Team::query()
            ->whereNotNull('teamId')
            ->where('sport', static::sport())
            ->where('active', true)
            ->pluck('id', 'teamId')
            ->all();
        foreach ($fxid as $gId):
            $response = Curl::to(static::url('games'))
                ->withHeader('x-apisports-key: ' . static::apiKey())
                ->asJsonResponse()
                ->withData(['id' => $gId])
                ->get();
            // find missing teams
            foreach ($response->response as $lg) {
                $homeTeamId = $teams[$lg->teams->home->id] ?? static::saveTeam($lg, $lg->teams->home);
                $awayTeamId = $teams[$lg->teams->away->id] ?? static::saveTeam($lg, $lg->teams->away);
                $gameId = $lg->id ?? $lg->game->id;
                Game::query()->updateOrCreate([
                    'gameId' => $gameId
                ], [
                    'slug' => Str::slug("{$gameId} {$lg->teams->home->name} vs {$lg->teams->away->name}-" . Carbon::parse($lg->date, timezone: $lg->timezone ?? 'UTC')->format('Y-m-d')),
                    'league_id' => $league->id,
                    'home_team_id' => $homeTeamId,
                    'away_team_id' => $awayTeamId,
                    'name' => "{$lg->teams->home->name} vs {$lg->teams->away->name}",
                    'startTime' => Carbon::parse($lg->date, timezone: $lg->timezone ?? 'UTC'),
                    'status' => $lg->status->short,
                    'sport' => static::sport(),
                    'closed' => false
                ]);
            }
        endforeach;
    }

    protected static function saveScores(Game $game, $lg)
    {
        foreach ($lg->scores as $score) {
            foreach (static::scoreTypes() as $type) {
                $game->scores()->updateOrCreate([
                    'type' => $type->value,
                ], [
                    'home' => $type->getScore($score->home),
                    'away' => $type->getScore($score->away),
                ]);
            }
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
