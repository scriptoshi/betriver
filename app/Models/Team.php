<?php

namespace App\Models;

use App\Enums\LeagueSport;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Team extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'teams';

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
            'active' => 'bool'
        ];
    }


    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'teamId',
        'name',
        'code',
        'country',
        'description',
        'image',
        'sport',
        'active'
    ];


    /**

     * Get the home_games this model Owns.
     *
     */
    public function home_games(): HasMany
    {
        return $this->hasMany(Game::class, 'home_team_id', 'id');
    }

    /**

     * Get the odds this model Owns.
     *
     */
    public function odds(): MorphMany
    {
        return $this->morphMany(Odd::class, 'game');
    }

    /**

     * Get the away_games this model Owns.
     *
     */
    public function away_games(): HasMany
    {
        return $this->hasMany(Game::class, 'away_team_id', 'id');
    }

    /**
     * Get the uploads this model Owns.
     *
     */
    public function uploads(): MorphMany
    {
        return $this->morphMany(Upload::class, 'uploadable',);
    }
}
