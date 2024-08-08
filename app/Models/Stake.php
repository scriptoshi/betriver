<?php

namespace App\Models;

use App\Enums\ScoreType;
use App\Enums\StakeStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stake extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'stakes';

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
            'scoreType' => ScoreType::class,
            'status' => StakeStatus::class,
            'won' => 'boolean',
            'is_withdrawn' => 'boolean',
            'allow_partial' => 'boolean'
        ];
    }

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'slip_id',
        'user_id',
        'bet_id',
        'game_id',
        'uid',
        'scoreType',
        'amount',
        'filled',
        'unfilled',
        'payout',
        'odds',
        'status',
        'won',
        'is_withdrawn',
        'allow_partial'
    ];


    /**

     * Get the user this model Belongs To.
     *
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**

     * Get the game this model Belongs To.
     *
     */
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class, 'game_id', 'id');
    }

    /**

     * Get the bet this model Belongs To.
     *
     */
    public function bet(): BelongsTo
    {
        return $this->belongsTo(Bet::class, 'bet_id', 'id');
    }

    /**

     * Get the maker_trades this model Owns.
     *
     */
    public function maker_trades(): HasMany
    {
        return $this->hasMany(Trade::class, 'maker_id', 'id');
    }

    /**

     * Get the taker_trades this model Owns.
     *
     */
    public function taker_trades(): HasMany
    {
        return $this->hasMany(Trade::class, 'taker_id', 'id');
    }
}
