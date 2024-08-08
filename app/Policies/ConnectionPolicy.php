<?php
/** dev:
    *Stephen Isaac:  ofuzak@gmail.com.
    *Skype: ofuzak
 */

namespace App\Policies;

use App\Models\Connection;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConnectionPolicy
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
        return $user->hasPermission('viewany.connection');
    }

    /**
     * Determine whether the user can view the Connection.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Connection  $connection
     * @return bool
     */
    public function view(User $user, Connection $connection): bool
    {
		return $user->hasPermission('view.connection');
    }

    /**
     * Determine whether the user can create DocDummyPluralModel.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
		return $user->hasPermission('create.connection');
    }

    /**
     * Determine whether the user can update the Connection.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Connection  $connection
     * @return bool
     */
    public function update(User $user, Connection $connection): bool
    {
        return $user->hasPermission('update.connection') || $user->id == $connection->user_id;
    }

    /**
     * Determine whether the user can delete the Connection.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Connection  $connection
     * @return bool
     */
    public function delete(User $user, Connection $connection): bool
    {
        return  $user->hasPermission('delete.connection') || $user->id == $connection->user_id;
    }

    /**
     * Determine whether the user can restore the Connection.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Connection  $connection
     * @return bool
     */
    public function restore(User $user, Connection $connection): bool
    {
         return $user->hasPermission('restore.connection') || $user->id == $connection->user_id;
    }

    /**
     * Determine whether the user can permanently delete the Connection.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Connection  $connection
     * @return bool
     */
    public function forceDelete(User $user, Connection $connection): bool
    {
        return $user->hasPermission('forcedelete.connection') || $user->id == $connection->user_id;		
    }
}