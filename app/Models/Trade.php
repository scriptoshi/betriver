<?php

namespace App\Models;

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


    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'maker_id',
        'taker_id',
        'amount',
        'buy',
        'sell',
        'margin'
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
}
