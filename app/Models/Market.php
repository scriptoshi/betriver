<?php

namespace App\Models;

use App\Contracts\BetMarket;
use App\Enums\Market as EnumsMarket;
use App\Enums\MarketCategory;
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
            'is_default' => 'boolean',
            'type' => EnumsMarket::class,
            'category' => MarketCategory::class
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
        'sequence',
        'oddsId',
        'category',
        'sport',
        'active',
        'bookie_active',
        'is_default'
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

     * Get the bets this model Owns.
     *
     */
    public function trades(): HasMany
    {
        return $this->hasMany(Trade::class, 'market_id', 'id');
    }
    /**

     * Get the bets this model Owns.
     *
     */
    public function stakes(): HasMany
    {
        return $this->hasMany(Stake::class, 'market_id', 'id');
    }

    /**

     * Get the bets this model Owns.
     *
     */
    public function odds(): HasMany
    {
        return $this->hasMany(Odd::class, 'market_id', 'id');
    }

    /**

     * Get the games this model Belongs To.
     *
     */
    public function games(): BelongsToMany
    {
        return $this->belongsToMany(Game::class, 'game_market', 'market_id', 'game_id')
            ->withTimestamps()
            ->withPivot(['bookie_active', 'winning_bet_id', 'active', 'id']);
    }

    /**

     * Get the games this model Belongs To.
     *
     */
    public function gameMarkets(): HasMany
    {
        return $this->hasMany(GameMarket::class);
    }

    /**
     * Get the Betting Market for this Market;
     */
    public function manager(): BetMarket
    {
        return $this->type->make($this->segment);
    }
}
