<?php


namespace App\Policies;

use App\Models\Commission;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommissionPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('viewany.commission');
    }

    /**
     * Determine whether the user can view the Commission.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Commission  $commission
     * @return bool
     */
    public function view(User $user, Commission $commission): bool
    {
        return $user->hasPermission('view.commission');
    }

    /**
     * Determine whether the user can create DocDummyPluralModel.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create.commission');
    }

    /**
     * Determine whether the user can update the Commission.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Commission  $commission
     * @return bool
     */
    public function update(User $user, Commission $commission): bool
    {
        return $user->hasPermission('update.commission') || $user->id == $commission->user_id;
    }

    /**
     * Determine whether the user can delete the Commission.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Commission  $commission
     * @return bool
     */
    public function delete(User $user, Commission $commission): bool
    {
        return  $user->hasPermission('delete.commission') || $user->id == $commission->user_id;
    }

    /**
     * Determine whether the user can restore the Commission.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Commission  $commission
     * @return bool
     */
    public function restore(User $user, Commission $commission): bool
    {
        return $user->hasPermission('restore.commission') || $user->id == $commission->user_id;
    }

    /**
     * Determine whether the user can permanently delete the Commission.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Commission  $commission
     * @return bool
     */
    public function forceDelete(User $user, Commission $commission): bool
    {
        return $user->hasPermission('forcedelete.commission') || $user->id == $commission->user_id;
    }
}
