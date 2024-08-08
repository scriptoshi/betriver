<?php

namespace App\Models;

use App\Enums\BetMode;
use App\Enums\GoalCount;
use App\Enums\Halfs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bet extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'bets';

    /**
     * The database primary key value.
     *
     * @var string
     */
    protected $primaryKey = 'id';



    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'market_id',
        'name',
        'sport',
        'result'
    ];


    /**

     * Get the market this model Belongs To.
     *
     */
    public function market(): BelongsTo
    {
        return $this->belongsTo(Market::class, 'market_id', 'id');
    }

    /**

     * Get the odds this model Owns.
     *
     */
    public function odds(): HasMany
    {
        return $this->hasMany(Odd::class, 'bet_id', 'id');
    }

    /**

     * Get the stakes this model Owns.
     *
     */
    public function stakes(): HasMany
    {
        return $this->hasMany(Stake::class, 'bet_id', 'id');
    }

    /**

     * Get the tickets this model Belongs To.
     *
     */
    public function tickets(): BelongsToMany
    {
        return $this->belongsToMany(Ticket::class, 'wagers', 'bet_id', 'ticket_id')->withPivot(['winner'])->withTimestamps();
    }

    /**

     * Get the wagers this model Owns.
     *
     */
    public function wagers(): HasMany
    {
        return $this->hasMany(Wager::class, 'bet_id', 'id');
    }

    /**

     * Get the winGames this model Belongs To.
     *
     */
    public function winGames(): BelongsToMany
    {
        return $this->belongsToMany(Game::class, 'bet_game', 'bet_id', 'game_id');
    }
}
