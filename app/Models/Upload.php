<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Storage;

class Upload extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'uploads';

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


    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uploadable',
        'key',
        'url',
        'path',
        'drive'
    ];


    /**
     * Get the uploadable the upload Belongs To.
     *
     */
    public function uploadable(): MorphTo
    {
        return $this->morphTo();
    }

    public function removeFile()
    {
        Storage::disk($this->disk)->delete($this->path);
    }
}
