<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Market extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'markets';

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
            'active' => 'boolean',
            'bookie_active' => 'boolean',
        ];
    }


    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'segment',
        'oddsId',
        'description',
        'sport',
        'active',
        'bookie_active'
    ];


    /**

     * Get the bets this model Owns.
     *
     */
    public function bets(): HasMany
    {
        return $this->hasMany(Bet::class, 'market_id', 'id');
    }

    /**

     * Get the games this model Belongs To.
     *
     */
    public function games(): BelongsToMany
    {
        return $this->belongsToMany(Game::class, 'game_market', 'market_id', 'game_id');
    }
}
