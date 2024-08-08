<?php

namespace App\Models;

use App\Enums\ScoreType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wager extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'wagers';

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
            'winner' => 'boolean'
        ];
    }

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ticket_id',
        'bet_id',
        'game_id',
        'odd_id',
        'scoreType',
        'odds',
        'winner'
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

     * Get the ticket this model Belongs To.
     *
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id', 'id');
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

     * Get the odd this model Belongs To.
     *
     */
    public function odd(): BelongsTo
    {
        return $this->belongsTo(Odd::class, 'odd_id', 'id');
    }
}
