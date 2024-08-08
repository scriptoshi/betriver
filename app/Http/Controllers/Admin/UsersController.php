<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\User as UserResource;
use App\Models\User;
use Inertia\Inertia;

use Illuminate\Http\Request;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request, $filter = null)
    {
        $keyword = $request->get('search');
        $perPage = 25;
        $query = User::query();
        if (!empty($keyword)) {
            $query->where('email', 'LIKE', "%$keyword%")
                ->orWhere('username', 'LIKE', "%$keyword%");
        }
        if ($filter == 'banned') {
            $query->whereNotNull('banned_at');
        }
        if ($filter == 'kyc') {
            $query->whereNull('kyc_verified_at');
        }
        if ($filter == 'email') {
            $query->whereNull('email_verified_at');
        }
        if ($filter == 'phone') {
            $query->whereNull('phone_verified_at');
        }
        if ($filter == 'balance') {
            $query->where('balance', '>', 0);
        }
        $usersItems = $query->latest()->paginate($perPage);
        return Inertia::render('Admin/Users/Index', [
            'users' => UserResource::collection($usersItems)
        ]);
    }

    /**
     * toggle status of  the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function toggle(Request $request, User $user)
    {
        $user->active = !$user->active;
        $user->save();
        return back()->with('success', $user->active ? __(' :name User Enabled !', ['name' => $user->name]) : __(' :name  User Disabled!', ['name' => $user->name]));
    }

    /**
     * toggle status of  the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function ban(Request $request, User $user)
    {
        $user->banned_at =  $user->banned_at ? null : now();
        $user->save();
        return back()->with('success', $user->banned_at ? __(' :name User Un banned !', ['name' => $user->name]) : __(' :name  User Banned!', ['name' => $user->name]));
    }
}
