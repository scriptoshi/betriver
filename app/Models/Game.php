<?php

namespace App\Models;

use App\Enums\LeagueSport;
use App\Enums\GameStatus;
use App\Enums\GoalCount;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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
            'status' => GameStatus::class,
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
        'gameId',
        'name',
        'startTime',
        'endTime',
        'status',
        'sport'
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

     * Get the markets this model Belongs To.
     *
     */
    public function markets(): BelongsToMany
    {
        return $this->belongsToMany(Market::class, 'game_market', 'game_id', 'market_id')
            ->withTimestamps()
            ->withPivot(['active', 'id']);
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

     * Get the trades this model Owns.
     *
     */
    public function trades(): HasMany
    {
        return $this->hasMany(Trade::class, 'game_id', 'id');
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
        return $this->morphMany(Odd::class, 'oddable');
    }

    /**

     * Get the winBets this model Belongs To.
     *
     */
    public function winBets(): BelongsToMany
    {
        return $this->belongsToMany(Bet::class, 'bet_game', 'game_id', 'bet_id');
    }
}
