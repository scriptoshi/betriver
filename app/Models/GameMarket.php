<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameMarket extends Model
{


    use HasUuid;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'game_market';

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
            'bookie_active' => 'boolean'
        ];
    }

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'active',
        'bookie_active',
        'winning_bet_id',
        'market_id',
        'game_id',
    ];

    /**
     * Get the game the game_market belongsTo
     */
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * Get the game the game_market belongsTo
     */
    public function market(): BelongsTo
    {
        return $this->belongsTo(Market::class);
    }

    /**
     * Get the game the game_market belongsTo
     */
    public function winningBet(): BelongsTo
    {
        return $this->belongsTo(Bet::class, 'winning_bet_id', 'id');
    }
}
