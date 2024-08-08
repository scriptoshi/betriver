<?php

namespace App\Models;

use App\Enums\LeagueSport;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class League extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'leagues';

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
            'sport' => LeagueSport::class,
            'active' => 'boolean'
        ];
    }

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'leagueId',
        'name',
        'description',
        'image'
    ];


    /**
     * Get the games this model Owns.
     *
     */
    public function games(): HasMany
    {
        return $this->hasMany(Game::class, 'league_id', 'id');
    }
    /**
     * Get the games this model Owns.
     *
     */
    public function uploads(): MorphMany
    {
        return $this->morphMany(Upload::class, 'uploadable',);
    }
}
