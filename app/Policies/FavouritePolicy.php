<?php


namespace App\Policies;

use App\Models\Favourite;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FavouritePolicy
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
        return $user->hasPermission('viewany.favourite');
    }

    /**
     * Determine whether the user can view the Favourite.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Favourite  $favourite
     * @return bool
     */
    public function view(User $user, Favourite $favourite): bool
    {
        return $user->hasPermission('view.favourite');
    }

    /**
     * Determine whether the user can create DocDummyPluralModel.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create.favourite');
    }

    /**
     * Determine whether the user can update the Favourite.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Favourite  $favourite
     * @return bool
     */
    public function update(User $user, Favourite $favourite): bool
    {
        return $user->hasPermission('update.favourite') || $user->id == $favourite->user_id;
    }

    /**
     * Determine whether the user can delete the Favourite.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Favourite  $favourite
     * @return bool
     */
    public function delete(User $user, Favourite $favourite): bool
    {
        return  $user->hasPermission('delete.favourite') || $user->id == $favourite->user_id;
    }

    /**
     * Determine whether the user can restore the Favourite.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Favourite  $favourite
     * @return bool
     */
    public function restore(User $user, Favourite $favourite): bool
    {
        return $user->hasPermission('restore.favourite') || $user->id == $favourite->user_id;
    }

    /**
     * Determine whether the user can permanently delete the Favourite.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Favourite  $favourite
     * @return bool
     */
    public function forceDelete(User $user, Favourite $favourite): bool
    {
        return $user->hasPermission('forcedelete.favourite') || $user->id == $favourite->user_id;
    }
}
