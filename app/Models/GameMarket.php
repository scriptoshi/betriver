<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class GameMarket extends Model
{


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
            'active' => 'boolean'
        ];
    }

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'active'
    ];
}
