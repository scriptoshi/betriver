<?php

namespace App\Models;

use App\Enums\LeagueSport;
use App\Enums\StakeStatus;
use App\Enums\StakeType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
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
            'type' => StakeType::class,
            'status' => StakeStatus::class,
            'won' => 'boolean',
            'is_withdrawn' => 'boolean',
            'allow_partial' => 'boolean',
            'is_trade_out' => 'boolean',
            'is_traded_out' => 'boolean',
            'sport' => LeagueSport::class
        ];
    }

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'original_stake_id',
        'bet_id',
        'market_id',
        'bet_type',
        'game_id',
        'uid',
        'amount',
        'filled',
        'unfilled',
        'qty',
        'payout',
        'liability',
        'profit_loss',
        'odds',
        'type',
        'status',
        'sport',
        'game_info',
        'bet_info',
        'market_info',
        'won',
        'is_withdrawn',
        'allow_partial',
        'is_trade_out',
        'is_traded_out'
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
    public function market(): BelongsTo
    {
        return $this->belongsTo(Market::class, 'bet_id', 'id');
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

    /**

     * Get the transactions of this stake;
     *
     */
    public function transactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'transactable');
    }
}
