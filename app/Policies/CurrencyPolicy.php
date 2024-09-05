<?php


namespace App\Policies;

use App\Models\Currency;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CurrencyPolicy
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
        return $user->hasPermission('viewany.currency');
    }

    /**
     * Determine whether the user can view the Currency.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Currency  $currency
     * @return bool
     */
    public function view(User $user, Currency $currency): bool
    {
		return $user->hasPermission('view.currency');
    }

    /**
     * Determine whether the user can create DocDummyPluralModel.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
		return $user->hasPermission('create.currency');
    }

    /**
     * Determine whether the user can update the Currency.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Currency  $currency
     * @return bool
     */
    public function update(User $user, Currency $currency): bool
    {
        return $user->hasPermission('update.currency') || $user->id == $currency->user_id;
    }

    /**
     * Determine whether the user can delete the Currency.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Currency  $currency
     * @return bool
     */
    public function delete(User $user, Currency $currency): bool
    {
        return  $user->hasPermission('delete.currency') || $user->id == $currency->user_id;
    }

    /**
     * Determine whether the user can restore the Currency.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Currency  $currency
     * @return bool
     */
    public function restore(User $user, Currency $currency): bool
    {
         return $user->hasPermission('restore.currency') || $user->id == $currency->user_id;
    }

    /**
     * Determine whether the user can permanently delete the Currency.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Currency  $currency
     * @return bool
     */
    public function forceDelete(User $user, Currency $currency): bool
    {
        return $user->hasPermission('forcedelete.currency') || $user->id == $currency->user_id;		
    }
}