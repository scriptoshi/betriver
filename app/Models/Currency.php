<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Currency extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'currencies';

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
        ];
    }


    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'gateway',
        'code',
        'symbol',
        'name',
        'regex',
        'logo_url',
        'chain',
        'contract',
        'explorer',
        'rate',
        'precision',
        'active'
    ];

    public function scopeActive($query)
    {
        $query->where('active', true);
    }
}
