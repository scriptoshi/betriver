<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    use SoftDeletes;

	/**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'feedback';

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
        'user_id',
		'feedback'
   ];

    
    /**

    * Get the user this model Belongs To.
    *
    */
    public function user():BelongsTo
	{
		return $this->belongsTo(User::class,'user_id','id');
	}

}
