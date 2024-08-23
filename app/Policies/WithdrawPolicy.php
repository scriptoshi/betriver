<?php


namespace App\Policies;

use App\Models\User;
use App\Models\Withdraw;
use Illuminate\Auth\Access\HandlesAuthorization;

class WithdrawPolicy
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
        return $user->hasPermission('viewany.withdraw');
    }

    /**
     * Determine whether the user can view the Withdraw.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Withdraw  $withdraw
     * @return bool
     */
    public function view(User $user, Withdraw $withdraw): bool
    {
        return $user->hasPermission('view.withdraw');
    }

    /**
     * Determine whether the user can create DocDummyPluralModel.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create.withdraw');
    }

    /**
     * Determine whether the user can update the Withdraw.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Withdraw  $withdraw
     * @return bool
     */
    public function update(User $user, Withdraw $withdraw): bool
    {
        return $user->hasPermission('update.withdraw') || $user->id == $withdraw->user_id;
    }

    /**
     * Determine whether the user can delete the Withdraw.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Withdraw  $withdraw
     * @return bool
     */
    public function delete(User $user, Withdraw $withdraw): bool
    {
        return  $user->hasPermission('delete.withdraw') || $user->id == $withdraw->user_id;
    }

    /**
     * Determine whether the user can restore the Withdraw.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Withdraw  $withdraw
     * @return bool
     */
    public function restore(User $user, Withdraw $withdraw): bool
    {
        return $user->hasPermission('restore.withdraw') || $user->id == $withdraw->user_id;
    }

    /**
     * Determine whether the user can permanently delete the Withdraw.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Withdraw  $withdraw
     * @return bool
     */
    public function forceDelete(User $user, Withdraw $withdraw): bool
    {
        return $user->hasPermission('forcedelete.withdraw') || $user->id == $withdraw->user_id;
    }
}
