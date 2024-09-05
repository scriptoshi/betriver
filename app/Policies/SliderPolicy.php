<?php


namespace App\Policies;

use App\Models\Slider;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SliderPolicy
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
        return $user->hasPermission('viewany.slider');
    }

    /**
     * Determine whether the user can view the Slider.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Slider  $slider
     * @return bool
     */
    public function view(User $user, Slider $slider): bool
    {
		return $user->hasPermission('view.slider');
    }

    /**
     * Determine whether the user can create DocDummyPluralModel.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
		return $user->hasPermission('create.slider');
    }

    /**
     * Determine whether the user can update the Slider.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Slider  $slider
     * @return bool
     */
    public function update(User $user, Slider $slider): bool
    {
        return $user->hasPermission('update.slider') || $user->id == $slider->user_id;
    }

    /**
     * Determine whether the user can delete the Slider.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Slider  $slider
     * @return bool
     */
    public function delete(User $user, Slider $slider): bool
    {
        return  $user->hasPermission('delete.slider') || $user->id == $slider->user_id;
    }

    /**
     * Determine whether the user can restore the Slider.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Slider  $slider
     * @return bool
     */
    public function restore(User $user, Slider $slider): bool
    {
         return $user->hasPermission('restore.slider') || $user->id == $slider->user_id;
    }

    /**
     * Determine whether the user can permanently delete the Slider.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Slider  $slider
     * @return bool
     */
    public function forceDelete(User $user, Slider $slider): bool
    {
        return $user->hasPermission('forcedelete.slider') || $user->id == $slider->user_id;		
    }
}