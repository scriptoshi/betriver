<?php

namespace App\Models;

use App\Enums\TransactionAction;
use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Transaction extends Model
{
    use SoftDeletes;
    use HasUuid;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'transactions';

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
            'action' => TransactionAction::class,
            'type' => TransactionType::class
        ];
    }

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'transactable_id',
        'transactable_type',
        'uuid',
        'description',
        'amount',
        'balance_before',
        'action',
        'type'
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

     * Get the transactable this model Belongs To.
     *
     */
    public function transactable(): MorphTo
    {
        return $this->morphTo();
    }
}
