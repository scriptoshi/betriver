<?php

namespace App\Models;

use App\Enums\ConnectionProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Connection extends Model
{
    use SoftDeletes;

	/**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'connections';

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
    protected function casts() {
		 return [
			'provider' => ConnectionProvider::class
		];
	}

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
		'provider',
		'userId'
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
