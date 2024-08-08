<?php

namespace App\Models;

use App\Traits\HasProfilePhoto;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasProfilePhoto;





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
            'banned_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_banned' => 'boolean',
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
    public function slips(): HasMany
    {
        return $this->hasMany(Slip::class, 'user_id', 'id');
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
            ->each(fn ($s) => trim($s))
            ->take($num)
            ->all();
        return User::query()
            ->whereIn('refId', $upline)
            ->latest('created_at')
            ->get();
    }
}
