<?php

namespace App\Api;

use App\Enums\LeagueSport;
use App\Enums\Nfl\GameStatus;
use App\Enums\Nfl\ScoreType;
use App\Models\Game;
use App\Models\League;
use App\Models\Team;
use Carbon\Carbon;
use Ixudra\Curl\Facades\Curl;
use Str;

class ApiNfl extends ApiSports
{


    public static function url($url)
    {
        return "https://v1.american-football.api-sports.io/$url";
    }


    public static function scoreTypes(): array
    {
        return ScoreType::cases();
    }

    public static function ended($status): bool
    {
        return GameStatus::tryFrom(strtoupper($status))?->ended() ?? true;
    }


    public static function sport(): LeagueSport
    {
        return LeagueSport::NFL;
    }

    /**
     * Update leagues
     * @return void
     */

    public static function updateLeagues()
    {
        $response = Curl::to(static::url('leagues'))
            ->withHeader('x-apisports-key: ' . static::apiKey())
            ->withData(['current' => 'true'])
            ->asJsonResponse()
            ->get();
        foreach ($response->response as $info) {
            foreach ($info->seasons as $season) {
                if (Carbon::parse($season->end)->gt(now())) {
                    $league = $info->league;
                    League::query()->updateOrCreate([
                        'leagueId' => $league->id,
                        'sport' => static::sport(),
                    ], [
                        'name' => $league->name,
                        'description' =>  $league->name,
                        'image' => $league->logo ?? null,
                        'country' => $lg->country->code ?? $lg->country->name ?? static::$country,
                        'season' => $season->year,
                        'season_ends_at' => Carbon::parse($season->end),
                    ]);
                    continue 2;
                }
            }
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
                $gameId = $lg->game->id;
                $startTime =  Carbon::parse($lg->game->date->date, timezone: $lg->game->date->timezone ?? 'UTC');
                $game = Game::query()->updateOrCreate([
                    'gameId' => $gameId
                ], [
                    'slug' => Str::slug("{$gameId} {$lg->teams->home->name} vs {$lg->teams->away->name}-" . $startTime->format('Y-m-d')),
                    'league_id' => $leagueId,
                    'home_team_id' => $homeTeamId,
                    'away_team_id' => $awayTeamId,
                    'name' => "{$lg->teams->home->name} vs {$lg->teams->away->name}",
                    'startTime' =>  $startTime,
                    'status' => $lg->game->status->short,
                    'sport' => static::sport(),
                    'closed' => false
                ]);
                if (static::ended($game->status)) {
                    static::saveScores($game, $lg);
                }
            }
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
        $game->status =  $lg->game->status?->short;
        if ($lg->game->status?->timer)
            $game->elapsed = intval($lg->game->status->timer);
        if (static::ended($game->status)) {
            $game->endTime = now();
            $game->closed = true;
        }
        $game->save();
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
        $lg =  League::query()->updateOrCreate([
            'leagueId' => $league->id,
            'sport' => static::sport(),
        ], [
            'name' => $league->name,
            'description' => $league->type ?? $league->name,
            'image' => $league->logo ?? null,
            'country' => $league->country->code ?? static::$country,
            'season' => $season,
            'season_ends_at' => $seasonEnds,
        ]);
        return  $lg->id;
    }
}
