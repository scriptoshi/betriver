<?php

namespace App\Api;

use App\Enums\LeagueSport;
use App\Enums\Mma\GameStatus;
use App\Enums\Mma\ScoreType;
use App\Models\Game;
use App\Models\League;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Ixudra\Curl\Facades\Curl;
use Str;

class ApiMma extends ApiSports
{


    public static function url($url)
    {
        return "https://v1.mma.api-sports.io/$url";
    }


    public static function scoreTypes(): array
    {
        return [];
    }

    public static function ended($status): bool
    {
        return GameStatus::tryFrom(strtoupper($status))?->ended() ?? true;
    }


    public static function sport(): LeagueSport
    {
        return LeagueSport::MMA;
    }

    /**
     * Update leagues
     * @return void
     */

    public static function updateLeagues()
    {
        $seasons =  Curl::to(static::url('seasons') . '')
            ->withHeader('x-apisports-key: ' . static::apiKey())
            ->asJsonResponse()
            ->get();
        $season = max($seasons->response);
        $response = Curl::to(static::url('categories') . '')
            ->withHeader('x-apisports-key: ' . static::apiKey())
            ->asJsonResponse()
            ->get();
        foreach ($response->response as $lg) {
            League::query()->updateOrCreate([
                'leagueId' => str($lg)->slug(),
                'sport' => static::sport(),
            ], [
                'name' => $lg,
                'description' => $lg,
                'image' => 'https://cdn.vox-cdn.com/uploads/chorus_asset/file/9178425/mma-192.0.0.png',
                'country' => null,
                'season' => $season,
                'season_ends_at' => Carbon::parse((string)$season)->endOfYear(),
            ]);
        }
    }



    public static function updateLiveGame(Collection $games)
    {
        $response = Curl::to(static::url('fights'))
            ->withHeader('x-apisports-key: ' . static::apiKey())
            ->asJsonResponse()
            ->get();
        $results = [];
        foreach ($response->response as $lg) {
            $game = Game::query()->where('gameId', $lg->id)->first();
            if (!$game) continue;
            $gameId = static::saveResults($game, $lg);
            if ($gameId) $results[] = $gameId;
        }
        if (count($results)) {
            static::fetchResults(collect($results));
        }
    }

    protected static function saveResults($game, $lg)
    {
        $winnerId = collect($lg->fighters)->first(fn($ft) => $ft->winner);
        $result = null;
        if ($winnerId) {
            $team = Team::query()->where('teamId', $winnerId->id)->first();
            if ($team) {
                $game->win_team_id =  $team->id;
                $game->save();
                $result = $game->gameId;
            }
        }
        $game->status = $lg->status->short;
        $game->elapsed = static::runningTimeToSeconds($lg->time ?? '00:00');
        if (static::ended($lg->status->short)) {
            $game->endTime = now();
            $game->closed = true;
        }
        $game->save();
        return $result;
    }

    public static function fetchResults(Collection  $fightIds)
    {
        if ($fightIds->count() > 1) $data = ['ids' => $fightIds->implode('-')];
        else $data = ['id' => $fightIds->first()];
        $response = Curl::to(static::url('fights/results/'))
            ->withHeader('x-apisports-key: ' . static::apiKey())
            ->asJsonResponse()
            ->withData($data)
            ->get();
        foreach ($response->response as $result) {
            $game = Game::where('gameId', $result->fight->id)->first();
            if (!$game) continue;
            $game->result = (array)$result;
            $game->save();
            //save scores
            if ($result->won_type === 'Points') {
                str($result->score[0])->explode('|')->map(function ($score, $num) use ($game) {
                    $type = match ($num) {
                        0 => ScoreType::JUDGE_ONE->value,
                        1 => ScoreType::JUDGE_TWO->value,
                        2 => ScoreType::JUDGE_THREE->value,
                    };
                    $game->scores()->updateOrCreate([
                        'type' => $type,
                    ], [
                        'home' => Str::before($score, '-'),
                        'away' => Str::after($score, '-'),
                    ]);
                });
            }
        }
    }

    /**
     * Load games
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
        $results = [];
        for ($i = 0; $i < ($days + 1); $i++) {
            $date = now()->addDays($i + $start)->format('Y-m-d');
            $response = Curl::to(static::url('fights'))
                ->withHeader('x-apisports-key: ' . static::apiKey())
                ->asJsonResponse()
                ->withData(['date' => $date])
                ->get();
            // find missing teams
            foreach ($response->response as $lg) {
                $leagueId = $leagues[str($lg->category)->slug()->value()] ?? static::saveLeague($lg->category);
                $homeTeamId = $teams[$lg->fighters->first->id] ?? static::saveTeam($lg, $lg->fighters->first);
                $awayTeamId = $teams[$lg->fighters->second->id] ?? static::saveTeam($lg, $lg->fighters->second);
                $date =  Carbon::parse($lg->date, timezone: $lg->timezone ?? 'UTC');
                $game = Game::query()->updateOrCreate([
                    'gameId' => $lg->id
                ], [
                    'slug' => Str::slug("{$lg->fighters->first->name} vs {$lg->fighters->second->name}" . '-' . $date->format('Y-m-d')),
                    'league_id' => $leagueId,
                    'home_team_id' => $homeTeamId,
                    'away_team_id' => $awayTeamId,
                    'name' => "{$lg->fighters->first->name} vs {$lg->fighters->second->name}",
                    'startTime' => $date,
                    'status' => $lg->status->short,
                    'sport' => static::sport(),
                    'rounds' => $lg->is_main ? 5 : 3,
                    'closed' => false
                ]);
                if (static::ended($lg->status->short)) {
                    $game->endTime = now();
                    $game->closed = true;
                    $gameId = static::saveResults($game, $lg);
                    if ($gameId) $results[] = $gameId;
                }
            }
        }
        if (count($results)) {
            collect($results)->chunk(10)->map(fn($chunk) => static::fetchResults($chunk));
        }
    }


    /**
     * Save a league from game object
     */
    public static function saveLeague($lg): int
    {
        $league =  League::query()->updateOrCreate([
            'leagueId' => str($lg)->slug(),
            'sport' => static::sport(),
        ], [
            'name' => $lg,
            'description' => $lg,
            'image' => 'https://cdn.vox-cdn.com/uploads/chorus_asset/file/9178425/mma-192.0.0.png',
            'country' => null,
            'season' => now()->year,
            'season_ends_at' => now()->endOfYear(),
        ]);
        return  $league->id;
    }

    /**
     * save MMA team (Player for now)
     */
    public static function saveTeam($game, $resTeam)
    {
        $team = Team::query()->updateOrCreate([
            'teamId' => $resTeam->id,
        ], [
            'name' => $resTeam->name,
            'code' => $resTeam->code ?? str($resTeam->name)->camel(),
            'country' => $game->country->code ??  static::$country ?? 'US',
            'description' =>  $resTeam->name,
            'sport' => static::sport(),
            'image' =>  $resTeam->logo
        ]);
        //$league->teams()->attach($team, ['season' => $league->season]);
        return $team->id;
    }
}
