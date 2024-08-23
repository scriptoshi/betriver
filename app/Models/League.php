<?php

namespace App\Models;

use App\Enums\LeagueSport;
use App\Enums\RaceTag;
use App\Support\Country;
use App\Support\LeagueSlug;
use Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class League extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'leagues';

    /**
     * The database primary key value.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be cast to native types.
     *
     * @var string
     */
    protected function casts()
    {
        return [
            'sport' => LeagueSport::class,
            'race_tag' => RaceTag::class,
            'active' => 'boolean',
            'menu' => 'boolean',
            'has_odds' => 'boolean',
            'season_ends_at' => 'datetime'
        ];
    }

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'leagueId',
        'name',
        'description',
        'image',
        'sport',
        'season',
        'race_tag',
        'season_ends_at',
        'country',
        'has_odds',
        'active',
        'menu',
        'slug', // Add slug to fillable array
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($league) {

            $league->slug =  $league->sport != LeagueSport::FOOTBALL
                ? Str::slug(trim($league->name . '-' . $league->leagueId  . ' ' . $league->country))
                : LeagueSlug::make($league->leagueId) ?? Str::slug(trim($league->name . ' ' . $league->sport->value . '-' . $league->leagueId  . ' ' . $league->country));
        });
    }



    /**
     * Get the games this model Owns.
     */
    public function games(): HasMany
    {
        return $this->hasMany(Game::class, 'league_id', 'id');
    }

    /**
     * Get the games live this model Owns.
     */
    public function live(): HasMany
    {
        return $this->games()->live();
    }

    /**
     * Get the games tomorrow this model Owns.
     */
    public function tomorrow(): HasMany
    {
        return $this->games()->tomorrow();
    }

    /**
     * Get the games thisWeek this model Owns.
     */
    public function thisWeek(): HasMany
    {
        return $this->games()->thisWeek();
    }

    /**
     * Get the games this model Owns.
     */
    public function nextWeek(): HasMany
    {
        return $this->games()->nextWeek();
    }

    /**
     * Get the games today this model Owns.
     */
    public function today(): HasMany
    {
        return $this->games()->today();
    }

    /**
     * Get the ended games this model Owns.
     */
    public function ended(): HasMany
    {
        return $this->games()->ended();
    }

    /**
     * Get the uploads this model Owns.
     */
    public function uploads(): MorphMany
    {
        return $this->morphMany(Upload::class, 'uploadable');
    }

    public static function getGameCountsBySport(LeagueSport $sport)
    {
        $cacheKey = "game_counts_{$sport->value}";
        $cacheDuration = now()->addMinutes(5); // Cache for 5 minutes
        return Cache::remember($cacheKey, $cacheDuration, function () use ($sport) {
            return static::where('sport', $sport)
                ->select('id')
                ->withCount([
                    'games as live_count' => function ($query) {
                        $query->live();
                    },
                    'games as today_count' => function ($query) {
                        $query->today();
                    },
                    'games as tomorrow_count' => function ($query) {
                        $query->tomorrow();
                    },
                    'games as this_week_count' => function ($query) {
                        $query->thisWeek();
                    },
                    'games as ended_count' => function ($query) {
                        $query->ended();
                    },
                ])
                ->get()
                ->pipe(function ($leagues) {
                    return [
                        'live' => $leagues->sum('live_count'),
                        'today' => $leagues->sum('today_count'),
                        'tomorrow' => $leagues->sum('tomorrow_count'),
                        'this_week' => $leagues->sum('this_week_count'),
                        'ended' => $leagues->sum('ended_count'),
                    ];
                });
        });
    }

    public static function clearGameCountsCache(LeagueSport $sport = null)
    {
        if ($sport) {
            Cache::forget("game_counts_{$sport->value}");
        } else {
            foreach (LeagueSport::cases() as $sportCase) {
                Cache::forget("game_counts_{$sportCase->value}");
            }
        }
    }


    public static function getLeaguesBySport()
    {
        $cacheKey = "leagues_by_sport";
        $cacheDuration = now()->addMinutes(30); // Cache for 30 minutes
        return Cache::remember($cacheKey, $cacheDuration, function () {
            $leagues = static::withCount(['games' => function ($query) {
                $query->where(function ($q) {
                    $q->where('endTime', '>', now())
                        ->orWhere('endTime', null);
                })->where('closed', false);
            }])
                ->where('menu', true)
                ->has('games')
                ->get()
                ->map(function ($league) {
                    return [
                        'id' => $league->id,
                        'name' => $league->name,
                        'slug' => $league->slug,
                        'count_games' => $league->games_count,
                        'sport' => $league->sport->value, // Include the sport
                    ];
                })
                ->groupBy('sport');
            // We don't need to convert sport keys here anymore as we're using the enum value directly
            return $leagues->all();
        });
    }

    public static function clearLeaguesBySport()
    {
        Cache::forget("leagues_by_sport");
    }


    public static function getLeaguesByCountry(LeagueSport $sport)
    {
        $cacheKey = "leagues_by_country_{$sport->value}";
        $cacheDuration = now()->addMinutes(30); // Cache for 30 minutes

        return Cache::remember($cacheKey, $cacheDuration, function () use ($sport) {
            $countries = collect(Country::$country)->keyBy('code');
            $leagues = static::whereNotNull('country')
                ->withCount(['games' => function ($query) {
                    $query->where(function ($q) {
                        $q->where('endTime', '>', now())
                            ->orWhere('endTime', null);
                    })->where('closed', false);
                }])
                ->where('sport', $sport)
                ->where('menu', true)
                ->get()
                ->map(function ($league) use ($countries) {
                    return [
                        'id' => $league->id,
                        'name' => $league->name,
                        'slug' => $league->slug,
                        'count_games' => $league->games_count,
                        'code' =>  $league->country,
                        'country' => $countries->get($league->country) ?? $league->country,
                    ];
                })
                ->groupBy('code')
                ->map(function ($countryLeagues) {
                    return $countryLeagues->sortByDesc('count_games')->values()->all();
                });

            return $leagues->all();
        });
    }

    public static function clearLeaguesByCountryCache()
    {
        Cache::forget("leagues_by_country");
    }

    public static function getDistinctNonCountryEntries()
    {
        return Cache::remember('distinct_non_country_entries', now()->addDay(), function () {
            return self::select('country')
                ->whereRaw('LENGTH(country) > 2')
                ->whereNotNull('country')
                ->distinct()
                ->pluck('country')
                ->values()
                ->all();
        });
    }

    public static function clearNonCountryEntriesCache()
    {
        Cache::forget('distinct_non_country_entries');
    }

    /**
     * clear cached methods
     */
    public static function clearCache()
    {
        static::clearGameCountsCache();
        static::clearLeaguesByCountryCache();
        static::clearLeaguesBySport();
        static::clearNonCountryEntriesCache();
    }
}
