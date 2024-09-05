<?php


namespace App\Policies;

use App\Models\Personal;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PersonalPolicy
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
        return $user->hasPermission('viewany.personal');
    }

    /**
     * Determine whether the user can view the Personal.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Personal  $personal
     * @return bool
     */
    public function view(User $user, Personal $personal): bool
    {
		return $user->hasPermission('view.personal');
    }

    /**
     * Determine whether the user can create DocDummyPluralModel.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
		return $user->hasPermission('create.personal');
    }

    /**
     * Determine whether the user can update the Personal.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Personal  $personal
     * @return bool
     */
    public function update(User $user, Personal $personal): bool
    {
        return $user->hasPermission('update.personal') || $user->id == $personal->user_id;
    }

    /**
     * Determine whether the user can delete the Personal.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Personal  $personal
     * @return bool
     */
    public function delete(User $user, Personal $personal): bool
    {
        return  $user->hasPermission('delete.personal') || $user->id == $personal->user_id;
    }

    /**
     * Determine whether the user can restore the Personal.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Personal  $personal
     * @return bool
     */
    public function restore(User $user, Personal $personal): bool
    {
         return $user->hasPermission('restore.personal') || $user->id == $personal->user_id;
    }

    /**
     * Determine whether the user can permanently delete the Personal.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Personal  $personal
     * @return bool
     */
    public function forceDelete(User $user, Personal $personal): bool
    {
        return $user->hasPermission('forcedelete.personal') || $user->id == $personal->user_id;		
    }
}