<?php


namespace App\Policies;

use App\Models\Feedback;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FeedbackPolicy
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
        return $user->hasPermission('viewany.feedback');
    }

    /**
     * Determine whether the user can view the Feedback.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Feedback  $feedback
     * @return bool
     */
    public function view(User $user, Feedback $feedback): bool
    {
		return $user->hasPermission('view.feedback');
    }

    /**
     * Determine whether the user can create DocDummyPluralModel.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
		return $user->hasPermission('create.feedback');
    }

    /**
     * Determine whether the user can update the Feedback.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Feedback  $feedback
     * @return bool
     */
    public function update(User $user, Feedback $feedback): bool
    {
        return $user->hasPermission('update.feedback') || $user->id == $feedback->user_id;
    }

    /**
     * Determine whether the user can delete the Feedback.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Feedback  $feedback
     * @return bool
     */
    public function delete(User $user, Feedback $feedback): bool
    {
        return  $user->hasPermission('delete.feedback') || $user->id == $feedback->user_id;
    }

    /**
     * Determine whether the user can restore the Feedback.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Feedback  $feedback
     * @return bool
     */
    public function restore(User $user, Feedback $feedback): bool
    {
         return $user->hasPermission('restore.feedback') || $user->id == $feedback->user_id;
    }

    /**
     * Determine whether the user can permanently delete the Feedback.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Feedback  $feedback
     * @return bool
     */
    public function forceDelete(User $user, Feedback $feedback): bool
    {
        return $user->hasPermission('forcedelete.feedback') || $user->id == $feedback->user_id;		
    }
}