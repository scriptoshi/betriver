<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;


class Slider extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sliders';

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
        'title',
        'description',
        'url',
        'button',
        'image',
        'active'
    ];


    /**
     * Get the uploads this model Owns.
     *
     */
    public function uploads(): MorphMany
    {
        return $this->morphMany(Upload::class, 'uploadable',);
    }
}
