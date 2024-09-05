<?php

namespace App\Models;

use App\Enums\PersonalBetEmails;
use App\Enums\PersonalLossLimitInterval;
use App\Enums\PersonalProofOfAddressType;
use App\Enums\PersonalProofOfIdentityType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Personal extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'personals';

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
            'proof_of_identity_type' => PersonalProofOfIdentityType::class,
            'proof_of_address_type' => PersonalProofOfAddressType::class,
            'bet_emails' => PersonalBetEmails::class,
            'mailing_list' => 'boolean',
            'confirm_bets' => 'boolean',
            'loss_limit_interval' => PersonalLossLimitInterval::class,
            'time_out_at' => 'datetime',
            'stake_limit_at' => 'datetime',
            'dob' => 'datetime'
        ];
    }

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'proof_of_identity',
        'proof_of_identity_type',
        'proof_of_address',
        'proof_of_address_type',
        'bet_emails',
        'mailing_list',
        'confirm_bets',
        'daily_gross_deposit',
        'weekly_gross_deposit',
        'monthly_gross_deposit',
        'loss_limit_interval',
        'loss_limit',
        'stake_limit',
        'time_out_at',
        'stake_limit_at',
        'dob'
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
     * Get the uploads this model Owns.
     *
     */
    public function uploads(): MorphMany
    {
        return $this->morphMany(Upload::class, 'uploadable',);
    }
}
