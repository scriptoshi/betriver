<?php

namespace App\Models;

use App\Enums\CommissionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Commission extends Model
{


    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'commissions';

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
            'type' => CommissionType::class,
            'active' => 'boolean'
        ];
    }

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'level',
        'percent',
        'active'
    ];


    /**

     * Get the payouts this model Owns.
     *
     */
    public function payouts(): HasMany
    {
        return $this->hasMany(Payout::class, 'commission_id', 'id');
    }
}
