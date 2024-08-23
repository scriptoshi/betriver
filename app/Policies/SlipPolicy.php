<?php



namespace App\Policies;

use App\Models\Slip;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SlipPolicy
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
        return $user->hasPermission('viewany.slip');
    }

    /**
     * Determine whether the user can view the Slip.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Slip  $slip
     * @return bool
     */
    public function view(User $user, Slip $slip): bool
    {
        return $user->hasPermission('view.slip');
    }

    /**
     * Determine whether the user can create DocDummyPluralModel.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create.slip');
    }

    /**
     * Determine whether the user can update the Slip.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Slip  $slip
     * @return bool
     */
    public function update(User $user, Slip $slip): bool
    {
        return $user->hasPermission('update.slip') || $user->id == $slip->user_id;
    }

    /**
     * Determine whether the user can delete the Slip.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Slip  $slip
     * @return bool
     */
    public function delete(User $user, Slip $slip): bool
    {
        return  $user->hasPermission('delete.slip') || $user->id == $slip->user_id;
    }

    /**
     * Determine whether the user can restore the Slip.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Slip  $slip
     * @return bool
     */
    public function restore(User $user, Slip $slip): bool
    {
        return $user->hasPermission('restore.slip') || $user->id == $slip->user_id;
    }

    /**
     * Determine whether the user can permanently delete the Slip.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Slip  $slip
     * @return bool
     */
    public function forceDelete(User $user, Slip $slip): bool
    {
        return $user->hasPermission('forcedelete.slip') || $user->id == $slip->user_id;
    }
}
