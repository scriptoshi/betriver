<?php


namespace App\Policies;

use App\Models\Payout;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PayoutPolicy
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
        return $user->hasPermission('viewany.payout');
    }

    /**
     * Determine whether the user can view the Payout.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Payout  $payout
     * @return bool
     */
    public function view(User $user, Payout $payout): bool
    {
        return $user->hasPermission('view.payout');
    }

    /**
     * Determine whether the user can create DocDummyPluralModel.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create.payout');
    }

    /**
     * Determine whether the user can update the Payout.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Payout  $payout
     * @return bool
     */
    public function update(User $user, Payout $payout): bool
    {
        return $user->hasPermission('update.payout') || $user->id == $payout->user_id;
    }

    /**
     * Determine whether the user can delete the Payout.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Payout  $payout
     * @return bool
     */
    public function delete(User $user, Payout $payout): bool
    {
        return  $user->hasPermission('delete.payout') || $user->id == $payout->user_id;
    }

    /**
     * Determine whether the user can restore the Payout.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Payout  $payout
     * @return bool
     */
    public function restore(User $user, Payout $payout): bool
    {
        return $user->hasPermission('restore.payout') || $user->id == $payout->user_id;
    }

    /**
     * Determine whether the user can permanently delete the Payout.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Payout  $payout
     * @return bool
     */
    public function forceDelete(User $user, Payout $payout): bool
    {
        return $user->hasPermission('forcedelete.payout') || $user->id == $payout->user_id;
    }
}
