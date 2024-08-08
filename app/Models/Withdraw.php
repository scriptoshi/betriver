<?php

namespace App\Models;

use App\Enums\WithdrawGateway;
use App\Enums\WithdrawStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Withdraw extends Model
{
    use SoftDeletes;
    use HasUuid;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'withdraws';

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
            'gateway' => WithdrawGateway::class,
            'data' => 'array',
            'status' => WithdrawStatus::class
        ];
    }

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'uuid',
        'gateway',
        'remoteId',
        'to',
        'gross_amount',
        'fees',
        'amount',
        'data',
        'status',
        //added
        'gateway_error',
        'batch_id',
        'fiat_currency',
        'crypto_currency',
        'crypto_amount',
    ];


    /**

     * Get the user this model Belongs To.
     *
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**

     * Get the transaction this model Belongs To.
     *
     */
    public function transaction(): MorphOne
    {
        return $this->morphOne(Transaction::class, 'transactable');
    }
}
