<?php

namespace App\Models;

use App\Enums\DepositGateway;
use App\Enums\DepositStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Deposit extends Model
{
    use SoftDeletes;
    use HasUuid;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'deposits';

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
            'gateway' => DepositGateway::class,
            'data' => 'array',
            'status' => DepositStatus::class
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
        'from',
        'gross_amount',
        'fees',
        'amount',
        'data',
        'status',
        // added
        'deposit_address',
        'amount_currency',
        'gateway_currency',
        'gateway_amount',
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


    /**

     * process the deposit.
     *
     */
    public function process()
    {
        return $this->gateway->driver()->deposit($this);
    }
}
