<?php

/** dev:
 *Stephen Isaac:  ofuzak@gmail.com.
 *Skype: ofuzak
 */

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransactionPolicy
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
        return $user->hasPermission('viewany.transaction');
    }

    /**
     * Determine whether the user can view the Transaction.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Transaction  $transaction
     * @return bool
     */
    public function view(User $user, Transaction $transaction): bool
    {
        return $user->hasPermission('view.transaction');
    }

    /**
     * Determine whether the user can create DocDummyPluralModel.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create.transaction');
    }

    /**
     * Determine whether the user can update the Transaction.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Transaction  $transaction
     * @return bool
     */
    public function update(User $user, Transaction $transaction): bool
    {
        return $user->hasPermission('update.transaction') || $user->id == $transaction->user_id;
    }

    /**
     * Determine whether the user can delete the Transaction.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Transaction  $transaction
     * @return bool
     */
    public function delete(User $user, Transaction $transaction): bool
    {
        return  $user->hasPermission('delete.transaction') || $user->id == $transaction->user_id;
    }

    /**
     * Determine whether the user can restore the Transaction.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Transaction  $transaction
     * @return bool
     */
    public function restore(User $user, Transaction $transaction): bool
    {
        return $user->hasPermission('restore.transaction') || $user->id == $transaction->user_id;
    }

    /**
     * Determine whether the user can permanently delete the Transaction.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Transaction  $transaction
     * @return bool
     */
    public function forceDelete(User $user, Transaction $transaction): bool
    {
        return $user->hasPermission('forcedelete.transaction') || $user->id == $transaction->user_id;
    }
}
