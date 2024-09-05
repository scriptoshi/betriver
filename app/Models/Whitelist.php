<?php

namespace App\Models;

use App\Enums\WhitelistStatus;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Whitelist extends Model
{
    use SoftDeletes, HasUuid;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'whitelists';

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
            'approved' => 'boolean',
            'status' => WhitelistStatus::class
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
        'currency_id',
        'payout_address',
        'approved',
        'approval_token',
        'removal_token',
        'status'
    ];

    public function scopeApproved($query)
    {
        $query->where('approved', true);
    }
    /**

     * Get the user this model Belongs To.
     *
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**

     * Get the currency this model Belongs To.
     *
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id', 'id');
    }
}
