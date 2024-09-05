<?php

namespace App\Models;

use App\Enums\CommonLanguage;
use App\Enums\OddsType;
use App\Enums\UserLevel;
use App\Traits\HasProfilePhoto;
use App\TwoFactorAuth\TwoFactorAuthenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasProfilePhoto, TwoFactorAuthenticatable;





    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'balance',
        'bonus',
        'profile_photo_path',
        'is_admin',
        'banned_at',
        'refId',
        'referrer',
        'hide_balance',
        'lang',
        'level',
        'requested_next_level',
        'odds_type',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'address_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'kyc_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'address_verified_at' => 'datetime',
            'banned_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_banned' => 'boolean',
            'hide_balance' => 'boolean',
            'lang' =>  CommonLanguage::class,
            'odds_type' => OddsType::class,
            'level' => UserLevel::class,
        ];
    }



    /**
     * Determine if a user has been assigned admin access
     * 
     */
    public function isAdmin(): bool
    {
        return  $this->is_admin;
    }

    /**
     * Determine is a user has a given permission
     * @param string $pemission. The Permission to check for.
     */
    public function hasPermission(string $pemission)
    {
        return false;
    }

    /**
     * Get user personal info.
     *
     */
    public function personal(): HasOne
    {
        return $this->hasOne(Personal::class);
    }

    /**
     * Get users whitelists.
     *
     */
    public function whitelists()
    {
        return $this->hasMany(Whitelist::class);
    }

    /**
     * Get users watchlist.
     *
     */
    public function watchlist(): BelongsToMany
    {
        return $this->belongsToMany(Game::class, 'game_user', 'user_id', 'game_id');
    }

    /**
     * Get users watchlist.
     *
     */
    public function favourites(): HasMany
    {
        return $this->hasMany(Favourite::class);
    }

    /**
     * Get users transactions.
     *
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get staks the user has.
     *
     */
    public function stakes(): HasMany
    {
        return $this->hasMany(Stake::class, 'user_id', 'id');
    }

    /**
     * Get staks the user has.
     *
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'user_id', 'id');
    }

    /**
     * Get upline of the ref.
     *
     */
    public function upline($num = 5): Collection
    {
        if (!$this->referral) return [];
        $upline = str($this->referral)
            ->explode(':')
            ->each(fn($s) => trim($s))
            ->take($num)
            ->all();
        return User::query()
            ->whereIn('refId', $upline)
            ->latest('created_at')
            ->get();
    }
}
