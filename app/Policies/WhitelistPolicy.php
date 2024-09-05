<?php


namespace App\Policies;

use App\Models\User;
use App\Models\Whitelist;
use Illuminate\Auth\Access\HandlesAuthorization;

class WhitelistPolicy
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
        return $user->hasPermission('viewany.whitelist');
    }

    /**
     * Determine whether the user can view the Whitelist.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Whitelist  $whitelist
     * @return bool
     */
    public function view(User $user, Whitelist $whitelist): bool
    {
		return $user->hasPermission('view.whitelist');
    }

    /**
     * Determine whether the user can create DocDummyPluralModel.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
		return $user->hasPermission('create.whitelist');
    }

    /**
     * Determine whether the user can update the Whitelist.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Whitelist  $whitelist
     * @return bool
     */
    public function update(User $user, Whitelist $whitelist): bool
    {
        return $user->hasPermission('update.whitelist') || $user->id == $whitelist->user_id;
    }

    /**
     * Determine whether the user can delete the Whitelist.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Whitelist  $whitelist
     * @return bool
     */
    public function delete(User $user, Whitelist $whitelist): bool
    {
        return  $user->hasPermission('delete.whitelist') || $user->id == $whitelist->user_id;
    }

    /**
     * Determine whether the user can restore the Whitelist.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Whitelist  $whitelist
     * @return bool
     */
    public function restore(User $user, Whitelist $whitelist): bool
    {
         return $user->hasPermission('restore.whitelist') || $user->id == $whitelist->user_id;
    }

    /**
     * Determine whether the user can permanently delete the Whitelist.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Whitelist  $whitelist
     * @return bool
     */
    public function forceDelete(User $user, Whitelist $whitelist): bool
    {
        return $user->hasPermission('forcedelete.whitelist') || $user->id == $whitelist->user_id;		
    }
}