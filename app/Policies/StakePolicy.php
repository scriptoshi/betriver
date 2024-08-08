<?php

/** dev:
 *Stephen Isaac:  ofuzak@gmail.com.
 *Skype: ofuzak
 */

namespace App\Policies;

use App\Models\Stake;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StakePolicy
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
        return $user->hasPermission('viewany.stake');
    }

    /**
     * Determine whether the user can view the Stake.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Stake  $stake
     * @return bool
     */
    public function view(User $user, Stake $stake): bool
    {
        return $user->hasPermission('view.stake');
    }

    /**
     * Determine whether the user can create DocDummyPluralModel.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create.stake');
    }

    /**
     * Determine whether the user can update the Stake.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Stake  $stake
     * @return bool
     */
    public function update(User $user, Stake $stake): bool
    {
        return $user->hasPermission('update.stake') || $user->id == $stake->user_id;
    }

    /**
     * Determine whether the user can delete the Stake.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Stake  $stake
     * @return bool
     */
    public function delete(User $user, Stake $stake): bool
    {
        return  $user->hasPermission('delete.stake') || $user->id == $stake->user_id;
    }

    /**
     * Determine whether the user can restore the Stake.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Stake  $stake
     * @return bool
     */
    public function restore(User $user, Stake $stake): bool
    {
        return $user->hasPermission('restore.stake') || $user->id == $stake->user_id;
    }

    /**
     * Determine whether the user can permanently delete the Stake.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Stake  $stake
     * @return bool
     */
    public function forceDelete(User $user, Stake $stake): bool
    {
        return $user->hasPermission('forcedelete.stake') || $user->id == $stake->user_id;
    }
}
