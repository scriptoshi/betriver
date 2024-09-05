<?php

namespace App\Models;

use App\Contracts\GameStatus;
use App\Enums\LeagueSport;
use App\Enums\GoalCount;
use App\Enums\StakeStatus;
use App\Enums\StakeType;
use App\Traits\HasUuid;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

use function Clue\StreamFilter\fun;

class Game extends Model
{
    use SoftDeletes;
    use HasUuid;



    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'games';

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
            'startTime' => 'datetime',
            'endTime' => 'datetime',
            'active' => 'boolean',
            'closed' => 'boolean',
            'result' => 'array',
            'sport' => LeagueSport::class
        ];
    }

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'league_id',
        'home_team_id',
        'away_team_id',
        'win_team_id',
        'gameId',
        'name',
        'startTime',
        'elapsed',
        'endTime',
        'status',
        'result',
        'rounds',
        'sport',
        'active',
        'closed',
    ];

    public function getScores($type, GoalCount|string $team): float|int
    {
        $team = is_string($team) ? GoalCount::from($team) : $team;
        $query = Score::query()->where('game_id', $this->id);
        if (is_array($type)) {
            $query->whereIn('type', $type);
        } else {
            $query->where('type', $type);
        }
        if ($team === GoalCount::HOME) {
            return  $query->sum('home');
        }
        if ($team === GoalCount::AWAY) {
            return  $query->sum('away');
        }
        if ($team === GoalCount::TOTAL) {
            return  $query->clone()->sum('away') +  $query->clone()->sum('home');
        }
    }
    /**

     * Get the scores this model Owns.
     *
     */
    public function scores(): HasMany
    {
        return $this->hasMany(Score::class, 'game_id', 'id');
    }

    /**

     * Get the league this model Belongs To.
     *
     */
    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class, 'league_id', 'id');
    }

    /**

     * Get the homeTeam this model Belongs To.
     *
     */
    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id', 'id');
    }

    /**

     * Get the awayTeam this model Belongs To.
     *
     */
    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id', 'id');
    }

    /**

     * Get the winner of the game/fight
     *
     */
    public function winner(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'win_team_id', 'id');
    }

    /**

     * Get the markets this model Belongs To.
     *
     */
    public function markets(): BelongsToMany
    {
        return $this->belongsToMany(Market::class, 'game_market', 'game_id', 'market_id')
            ->withTimestamps()
            ->withPivot(['bookie_active', 'active', 'id']);
    }

    /**'active',

     * Get the markets this model Belongs To.
     *
     */
    public function activeMarkets(): BelongsToMany
    {
        return $this->markets()->wherePivot('active', true);
    }


    /**'active',

     * Get the markets this model Belongs To.
     *
     */
    public function activeBookieMarkets(): BelongsToMany
    {
        return $this->markets()->wherePivot('bookie_active', true);
    }



    /**

     * Get the stakes this model Owns.
     *
     */
    public function stakes(): HasMany
    {
        return $this->hasMany(Stake::class, 'game_id', 'id');
    }

    /**
     * Get the unmatched back stakes this model Owns.
     *
     */
    public function backs(): HasMany
    {
        return $this->hasMany(Stake::class, 'game_id', 'id')
            ->where('type', StakeType::BACK)
            ->whereIn('status', [StakeStatus::PENDING->value, StakeStatus::PARTIAL->value]);
    }

    /**
     * Get the unmatched back stakes this model Owns.
     *
     */
    public function bestBack(): HasOne
    {
        return $this->hasOne(Stake::class, 'game_id', 'id')
            ->where('type', StakeType::BACK)
            ->whereIn('status', [StakeStatus::PENDING->value, StakeStatus::PARTIAL->value])
            ->ofMany('odds', 'max');
    }



    /**
     * Get the unmatched lay stakes this model Owns.
     *
     */
    public function lays(): HasMany
    {
        return $this->hasMany(Stake::class, 'game_id', 'id')
            ->where('type', StakeType::LAY)
            ->whereIn('status', [StakeStatus::PENDING->value, StakeStatus::PARTIAL->value]);
    }

    /**
     * Get the unmatched lay stakes this model Owns.
     *
     */
    public function bestlay(): HasOne
    {
        return $this->hasOne(Stake::class, 'game_id', 'id')
            ->where('type', StakeType::LAY)
            ->whereIn('status', [StakeStatus::PENDING->value, StakeStatus::PARTIAL->value])
            ->ofMany('odds', 'min');
    }


    /**

     * Get the trades this model Owns.
     *
     */
    public function trades(): HasMany
    {
        return $this->hasMany(Trade::class, 'game_id', 'id');
    }

    /**'
     * Get the latest trade for each bet, 
     * #TODO. Get a more laravel method to get it done. Too much MYSQL
     */
    public function lastTrades()
    {

        return $this->hasMany(Trade::class)
            ->select('*')
            ->whereIn('id', function ($q) {
                $q->select(DB::raw('MAX(id) FROM messages GROUP BY author_id'));
            });
    }

    /**
     * Get the odds this model Owns.
     *
     */
    public function last_trade(): HasOne
    {
        return $this->hasOne(Trade::class)->latest();
    }

    /**

     * Get the tickets this model Belongs To.
     *
     */
    public function tickets(): BelongsToMany
    {
        return $this->belongsToMany(Ticket::class, 'wagers', 'game_id', 'ticket_id')->withPivot(['winner', 'odds'])->withTimestamps();
    }

    /**

     * Get the wagers this model Owns.
     *
     */
    public function wagers(): HasMany
    {
        return $this->hasMany(Wager::class, 'game_id', 'id');
    }

    /**

     * Get the odds this model Owns.
     *
     */
    public function odds(): MorphMany
    {
        return $this->morphMany(Odd::class, 'game');
    }

    /**

     * Get the winBets this model Belongs To.
     *
     */
    public function winBets(): BelongsToMany
    {
        return $this->belongsToMany(Bet::class, 'bet_game', 'game_id', 'bet_id');
    }


    public function state(): GameStatus
    {
        return $this->sport->gameStatus($this->status);
    }

    ## QUERY SCOPES

    /**
     * Scope a query to only include live games.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLive($query)
    {
        return $query->where(fn($q) => $q->where('endTime', null)->orWhere('closed', false))
            ->where('startTime', '<=', now())
            ->where('startTime', '>=', now()->subHours(5))
        ;
    }

    /**
     * Scope a query to only include games for today.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeToday($query)
    {
        return $query->whereDate('startTime', Carbon::today());
    }

    /**
     * Scope a query to only include games for tomorrow.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTomorrow($query)
    {
        return $query->whereDate('startTime', Carbon::tomorrow());
    }

    /**
     * Scope a query to only include games for this week.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('startTime', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    /**
     * Scope a query to only include games in next 7 days.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInNext7Days($query)
    {
        return $query->whereBetween('startTime', [now(), now()->addWeek()]);
    }

    /**
     * Scope a query to only include games for next week.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNextWeek($query)
    {
        $nextWeekStart = now()->addWeek()->startOfWeek();
        $nextWeekEnd = now()->addWeek()->endOfWeek();
        return $query->whereBetween('startTime', [$nextWeekStart, $nextWeekEnd]);
    }

    /**
     * Scope a query to only include ended games.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEnded($query)
    {
        return $query->where('closed', true)
            ->orWhere('endTime', '<=', now());
    }

    /**
     * return statistic for count by sport.
     * [
     *      'football' => 50,
     *      'basketball' => 30,
     *      'tennis' => 20,
     *      ... other sports
     * ]
     */
    public static function getActiveCountsBySport()
    {
        $cacheKey = "active_game_counts";
        $cacheDuration = now()->addMinutes(5); // Cache for 5 minutes

        return Cache::remember($cacheKey, $cacheDuration, function () {
            $counts = static::query()
                ->select('sport')
                ->selectRaw('COUNT(*) as active_count')
                ->where('active', true)
                ->where(function ($query) {
                    $query->where('endTime', '>', now())
                        ->orWhere('endTime', null);
                })
                ->where('closed', false)
                ->groupBy('sport')
                ->get();

            return $counts->mapWithKeys(function ($item) {
                // Convert the sport string to LeagueSport enum
                // $sportEnum = LeagueSport::from($item->sport);
                return [$item->sport => $item->active_count];
            })->all();
        });
    }

    public static function clearActiveCountsCache()
    {
        Cache::forget("active_game_counts");
    }

    /**
     * The getCountsBySport method will return an array with the following structure:
     *      [
     *         'sport' => 'football',
     *          'total' => 1000,
     *          'live' => 10,
     *          'today' => 50,
     *          'tomorrowt' => 45,
     *          'this_week' => 200,
     *          'ended' => 700
     *       ]
     * When called without a sport, it will return an array of these structures, keyed by sport.
     */
    public static function getCountsBySport()
    {
        $cacheKey = "game_total_counts_all";
        $cacheDuration = now()->addMinutes(10); // Cache for 10 minutes
        return Cache::remember($cacheKey, $cacheDuration, function () {
            $query = static::query();
            return $query->select('sport')
                ->selectRaw('COUNT(*) as total')
                ->selectRaw('SUM(CASE WHEN active = 1 AND (endTime IS NULL OR closed = false) AND startTime <= ? THEN 1 ELSE 0 END) as live', [now()])
                ->selectRaw('SUM(CASE WHEN DATE(startTime) = ? THEN 1 ELSE 0 END) as today', [now()->toDateString()])
                ->selectRaw('SUM(CASE WHEN DATE(startTime) = ? THEN 1 ELSE 0 END) as tomorrow', [now()->addDay()->toDateString()])
                ->selectRaw('SUM(CASE WHEN startTime BETWEEN ? AND ? THEN 1 ELSE 0 END) as this_week', [now()->startOfWeek(), now()->endOfWeek()])
                ->selectRaw('SUM(CASE WHEN startTime BETWEEN ? AND ? THEN 1 ELSE 0 END) as next_week', [now()->addWeek()->startOfWeek(), now()->addWeek()->endOfWeek()])
                ->selectRaw('SUM(CASE WHEN closed = 1 OR endTime <= ? THEN 1 ELSE 0 END) as ended', [now()])
                ->groupBy('sport')
                ->get()
                ->keyBy(fn($m) => $m->sport->value)
                ->map
                ->toArray()
                ->all();
        });
    }

    public static function clearCountsCache(LeagueSport $sport = null)
    {
        if ($sport) {
            Cache::forget("game_total_counts_{$sport->value}");
        } else {
            Cache::forget("game_total_counts_all");
            foreach (LeagueSport::cases() as $sportCase) {
                Cache::forget("game_total_counts_{$sportCase->value}");
            }
        }
    }


    /**
     * return statistic for count by sport.
     * [
     *      'football' => 50,
     *      'basketball' => 30,
     *      'tennis' => 20,
     *      ... other sports
     * ]
     */
    public static function getLiveCount()
    {
        $cacheKey = "all_live_game_counts";
        $cacheDuration = now()->addMinutes(5); // Cache for 5 minutes
        return Cache::remember($cacheKey, $cacheDuration, function () {
            return static::query()
                ->where('active', true)
                ->where(function ($query) {
                    $query->where('endTime', '>', now())
                        ->orWhere('endTime', null);
                })
                ->where('closed', false)
                ->count();
        });
    }

    public static function clearLiveCount()
    {
        Cache::forget("all_live_game_counts");
    }

    /**
     * clear cached methods
     */
    public static function clearCache()
    {
        static::clearActiveCountsCache();
        static::clearCountsCache();
        static::clearLiveCount();
    }
}
