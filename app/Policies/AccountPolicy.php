<?php
/** dev:
    *Stephen Isaac:  ofuzak@gmail.com.
    *Skype: ofuzak
 */

namespace App\Policies;

use App\Models\Account;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AccountPolicy
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
        return $user->hasPermission('viewany.account');
    }

    /**
     * Determine whether the user can view the Account.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Account  $account
     * @return bool
     */
    public function view(User $user, Account $account): bool
    {
		return $user->hasPermission('view.account');
    }

    /**
     * Determine whether the user can create DocDummyPluralModel.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
		return $user->hasPermission('create.account');
    }

    /**
     * Determine whether the user can update the Account.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Account  $account
     * @return bool
     */
    public function update(User $user, Account $account): bool
    {
        return $user->hasPermission('update.account') || $user->id == $account->user_id;
    }

    /**
     * Determine whether the user can delete the Account.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Account  $account
     * @return bool
     */
    public function delete(User $user, Account $account): bool
    {
        return  $user->hasPermission('delete.account') || $user->id == $account->user_id;
    }

    /**
     * Determine whether the user can restore the Account.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Account  $account
     * @return bool
     */
    public function restore(User $user, Account $account): bool
    {
         return $user->hasPermission('restore.account') || $user->id == $account->user_id;
    }

    /**
     * Determine whether the user can permanently delete the Account.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Account  $account
     * @return bool
     */
    public function forceDelete(User $user, Account $account): bool
    {
        return $user->hasPermission('forcedelete.account') || $user->id == $account->user_id;		
    }
}