<?php

namespace App\Api;

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
        return settings('site.apifootball_api_key');
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
        $response = Curl::to(static::url('leagues') . '')
            ->withHeader('x-apisports-key: ' . static::apiKey())
            ->withData(['current' => true])
            ->asJsonResponse()
            ->get();
        foreach ($response->response as $lg) {
            League::query()->updateOrCreate([
                'leagueId' => $lg->id,
                'sport' => static::sport(),
            ], [
                'name' => $lg->name,
                'description' => $lg->name,
                'image' => $lg->logo,
                'country' => $lg->country->code ?? static::$country,
                'season' => $lg->season ?? $lg->seasons[0]?->year
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
            $teams = [];
            foreach ($response?->response as $lg) {
                $team = Team::query()->updateOrCreate([
                    'teamId' => $lg->id,
                ], [
                    'name' => $lg->name,
                    'code' => $lg->code ?? str($lg->name)->camel(),
                    'country' => $lg->country->code ??  static::$country,
                    'description' =>  $lg->name,
                    'sport' => static::sport(),
                    'image' =>  $lg->logo
                ]);
                $teams[] = $team->id;
            }
            $league->teams()->attach($teams, ['season' => $league->season]);
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
            ->where('sport', static::sport())
            ->where('active', true)
            ->get();
        $teams = Team::query()
            ->whereNotNull('teamId')
            ->where('sport', static::sport())
            ->pluck('id', 'teamId')
            ->all();
        foreach ($leagues as $league) :
            $futureGames = settings('site.load_games_for_days', 14);
            $response = Curl::to(static::url('games'))
                ->withHeader('x-apisports-key: ' . static::apiKey())
                ->asJsonResponse()
                ->withData([
                    'league' => $league->leagueId,
                    'season' => $league->season,
                    'from' => now()->format('Y-m-d'),
                    'to' => now()->addDays($futureGames)->format('Y-m-d')
                ])
                ->get();
            foreach ($response->response as $lg) {
                $gameId = $lg->id ?? $lg->game->id;
                Game::query()->updateOrCreate([
                    'gameId' => $gameId
                ], [
                    'slug' => Str::slug("{$gameId} {$lg->teams->away->name} vs {$lg->teams->home->name}-" . Carbon::parse($lg->fixture->date)->format('Y-m-d')),
                    'league_id' => $league->id,
                    'home_team_id' => $teams[$lg->teams->home->id],
                    'away_team_id' => $teams[$lg->teams->away->id],
                    'name' => "{$lg->teams->away->name} vs {$lg->teams->home->name}",
                    'startTime' => Carbon::parse($lg->date, timezone: $lg->timezone ?? 'UTC'),
                    'status' => $lg->status->short,
                    'sport' => static::sport(),
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
        $response = Curl::to(static::url('games'))
            ->withHeader('x-apisports-key: ' . static::apiKey())
            ->asJsonResponse()
            ->withData($data)
            ->get();
        foreach ($response->response as $lg) {
            $game = Game::query()->where('gameId', $lg->game->id)->first();
            if (!$game) continue;
            foreach ($lg->scores as $score) {
                foreach (static::scoreTypes() as $type) {
                    $game->scores()->createOrUpdate([
                        'type' => $type->value,
                    ], [
                        'home' => $type->getScore($score->home),
                        'away' => $type->getScore($score->away),
                    ]);
                }
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

    public static function getOdds(Game $game)
    {
        $response = Curl::to(static::url('odds'))
            ->withHeader('x-apisports-key: ' . static::apiKey())
            ->withData(['game' => $game->gameId])
            ->asJsonResponse()
            ->get();
        $upserts = [];
        foreach ($response->bookmakers as $bookie) {
            foreach ($bookie->bets as $odd) {
                $upsert = static::getUpserts($game, $odd);
                $upserts =  [...$upserts, ...$upsert];
            }
        }
        $updates = collect($upserts)->unique('md5')->all();
        Odd::upsert($updates, uniqueBy: ['md5'], update: ['odd']);
    }

    public static function getUpserts(Game $game, $odd): array
    {
        $all = collect($odd->values)->map(fn ($o) => static::betName($o->value))->all();
        $odds = collect($odd->values)
            ->flatMap(fn ($o) => [static::betName($o->value) => $o->odd])
            ->all();
        $market = Market::query()
            ->with(['bets' => fn (Builder $q) => $q->whereIn('result', $all)])
            ->where('oddsId', $odd->id)->first();
        return $market->bets->map(function (Bet $bet) use ($odds, $odd, $game) {
            return [
                'bet_id' =>  $bet->id,
                'market_id' =>  $bet->market_id,
                'game_id' => $game->id,
                'bookie' => $odd->name,
                'game_type' => $game->getMorphClass(),
                'md5' => md5($bet->market_id . '-' . $bet->id . '-' . $game->id . '-' . $game->getMorphClass()),
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
}
