<?php

/** dev:
 *Stephen Isaac:  ofuzak@gmail.com.
 *Skype: ofuzak
 */

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TicketPolicy
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
        return $user->hasPermission('viewany.ticket');
    }

    /**
     * Determine whether the user can view the Ticket.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Ticket  $ticket
     * @return bool
     */
    public function view(User $user, Ticket $ticket): bool
    {
        return $user->hasPermission('view.ticket');
    }

    /**
     * Determine whether the user can create DocDummyPluralModel.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create.ticket');
    }

    /**
     * Determine whether the user can update the Ticket.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Ticket  $ticket
     * @return bool
     */
    public function update(User $user, Ticket $ticket): bool
    {
        return $user->hasPermission('update.ticket') || $user->id == $ticket->user_id;
    }

    /**
     * Determine whether the user can delete the Ticket.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Ticket  $ticket
     * @return bool
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        return  $user->hasPermission('delete.ticket') || $user->id == $ticket->user_id;
    }

    /**
     * Determine whether the user can restore the Ticket.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Ticket  $ticket
     * @return bool
     */
    public function restore(User $user, Ticket $ticket): bool
    {
        return $user->hasPermission('restore.ticket') || $user->id == $ticket->user_id;
    }

    /**
     * Determine whether the user can permanently delete the Ticket.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Ticket  $ticket
     * @return bool
     */
    public function forceDelete(User $user, Ticket $ticket): bool
    {
        return $user->hasPermission('forcedelete.ticket') || $user->id == $ticket->user_id;
    }
}
