<?php

namespace App\Models;

use App\Enums\ScoreType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Score extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'scores';

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
        'game_id',
        'type',
        'home',
        'away'
    ];


    /**

     * Get the game this model Belongs To.
     *
     */
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class, 'game_id', 'id');
    }
}
