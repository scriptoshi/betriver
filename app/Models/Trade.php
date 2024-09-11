<?php

namespace App\Models;

use App\Enums\TradeStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trade extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'trades';

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

            'status' => TradeStatus::class,

        ];
    }

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'maker_id',
        'bet_id',
        'market_id',
        'taker_id',
        'game_id',
        'amount',
        'price',
        'layer_price',
        'status',
        // admin arbitrage info 
        'buy', // bet lay odds
        'sell', // bet back odds
        'margin', // how much abitrage admin made on this trade
        'commission' // admin fees (depends on winner level)
    ];


    /**

     * Get the maker this model Belongs To.
     *
     */
    public function maker(): BelongsTo
    {
        return $this->belongsTo(Stake::class, 'maker_id', 'id');
    }

    /**

     * Get the taker this model Belongs To.
     *
     */
    public function taker(): BelongsTo
    {
        return $this->belongsTo(Stake::class, 'taker_id', 'id');
    }

    /**
     * Get the taker this model Belongs To.
     *
     */
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class, 'game_id', 'id');
    }
    /**
     * Get the taker this model Belongs To.
     *
     */
    public function bet(): BelongsTo
    {
        return $this->belongsTo(Bet::class, 'bet_id', 'id');
    }
    /**
     * Get the taker this model Belongs To.
     *
     */
    public function market(): BelongsTo
    {
        return $this->belongsTo(Market::class, 'market_id', 'id');
    }
}
