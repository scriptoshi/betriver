<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Odd extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'odds';

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
            'active' => 'boolean'
        ];
    }


    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'bet_id',
        'game_id',
        'game_type',
        'market_id',
        'oddId',
        'odd',
        'md5',
        'active',
    ];


    /**

     * Get the bet this model Belongs To.
     *
     */
    public function bet(): BelongsTo
    {
        return $this->belongsTo(Bet::class, 'bet_id', 'id');
    }

    /**

     * Get the game this model Belongs To.
     *
     */
    public function game(): MorphTo
    {
        return $this->morphTo(Game::class);
    }
    /**

     * Get the game this model Belongs To.
     *
     */
    public function market(): BelongsTo
    {
        return $this->belongsTo(Market::class, 'market_id', 'id');
    }
}
