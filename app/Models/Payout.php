<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\HasUuid; 
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Payout extends Model
{
    use SoftDeletes;
use HasUuid; 

	/**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'payouts';

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
        'commission_id',
		'user_id',
		'referral_id',
		'uuid',
		'description',
		'amount',
		'percent'
   ];

    
    /**

    * Get the commission this model Belongs To.
    *
    */
    public function commission():BelongsTo
	{
		return $this->belongsTo(Commission::class,'commission_id','id');
	}
	
    /**

    * Get the user this model Belongs To.
    *
    */
    public function user():BelongsTo
	{
		return $this->belongsTo(User::class,'user_id','id');
	}
	
    /**

    * Get the referral this model Belongs To.
    *
    */
    public function referral():BelongsTo
	{
		return $this->belongsTo(User::class,'referral_id','id');
	}
	
    /**

    * Get the transaction this model Belongs To.
    *
    */
    public function transaction():MorphOne
	{
		return $this->morphOne(Transaction::class,'transactable');
	}

}
