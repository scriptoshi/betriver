<?php
/** dev:
    *Stephen Isaac:  ofuzak@gmail.com.
    *Skype: ofuzak
 */

namespace App\Policies;

use App\Models\Deposit;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DepositPolicy
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
        return $user->hasPermission('viewany.deposit');
    }

    /**
     * Determine whether the user can view the Deposit.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Deposit  $deposit
     * @return bool
     */
    public function view(User $user, Deposit $deposit): bool
    {
		return $user->hasPermission('view.deposit');
    }

    /**
     * Determine whether the user can create DocDummyPluralModel.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
		return $user->hasPermission('create.deposit');
    }

    /**
     * Determine whether the user can update the Deposit.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Deposit  $deposit
     * @return bool
     */
    public function update(User $user, Deposit $deposit): bool
    {
        return $user->hasPermission('update.deposit') || $user->id == $deposit->user_id;
    }

    /**
     * Determine whether the user can delete the Deposit.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Deposit  $deposit
     * @return bool
     */
    public function delete(User $user, Deposit $deposit): bool
    {
        return  $user->hasPermission('delete.deposit') || $user->id == $deposit->user_id;
    }

    /**
     * Determine whether the user can restore the Deposit.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Deposit  $deposit
     * @return bool
     */
    public function restore(User $user, Deposit $deposit): bool
    {
         return $user->hasPermission('restore.deposit') || $user->id == $deposit->user_id;
    }

    /**
     * Determine whether the user can permanently delete the Deposit.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Deposit  $deposit
     * @return bool
     */
    public function forceDelete(User $user, Deposit $deposit): bool
    {
        return $user->hasPermission('forcedelete.deposit') || $user->id == $deposit->user_id;		
    }
}