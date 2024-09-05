<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TransactionAction;
use App\Enums\TransactionType;
use App\Enums\UserLevel;
use App\Http\Controllers\Controller;
use App\Http\Resources\Personal;
use App\Http\Resources\User as UserResource;
use App\Models\Stake;
use App\Models\User;
use App\Models\Wager;
use App\TwoFactorAuth\Actions\DisableTwoFactorAuthentication;
use Illuminate\Database\Eloquent\Builder;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\RedirectResponse;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

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
        $query = User::query()->with('personal');
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
        if ($filter == 'level-request') {
            $query->where('requested_next_level', true);
        }

        if ($filter == 'level-one') {
            $query->where('level', UserLevel::ONE);
        }
        if ($filter == 'level-two') {
            $query->where('level', UserLevel::TWO);
        }

        if ($filter == 'level-three') {
            $query->where('level', UserLevel::THREE);
        }

        $usersItems = $query->latest()->paginate($perPage);
        return Inertia::render('Admin/Users/Index', [
            'users' => UserResource::collection($usersItems)
        ]);
    }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function show(Request $request, User $user)
    {


        return Inertia::render('Admin/Users/Show', [
            'user' => new UserResource($user),
            'personal' => new Personal($user->personal),
            'direct' => User::where('referral', 'LIKE', $user->refId . "%")
                ->count(),
            'downline' => User::where('referral', 'LIKE', "%" . $user->refId . "%")
                ->count(),
            'stats' => [
                'Balance' => $user->balance,
                'Deposits' => $user->transactions()->where('type', TransactionType::DEPOSIT)->sum('amount'),
                'Withdraws' =>  $user->transactions()->where('type', TransactionType::WITHDRAW)->sum('amount'),
                'Lifetime Bets' => $user->transactions()->where('type', TransactionType::BET)->sum('amount'),
            ],
            'statistics' => [
                'Winnings' => $user->transactions()->where('type', TransactionType::WIN)->sum('amount'),
                'Losses' => $user->transactions()
                    ->where('type', TransactionType::BET)
                    ->whereHasMorph(
                        'transactable',
                        [Stake::class, Wager::class],
                        function (Builder $query) {
                            $query->where('won', false);
                        }
                    )
                    ->sum('amount'),
                'Referrals' => $user->transactions()->where('type', TransactionType::REFERRAL_COMMISSION)->sum('amount'),
                'Exchange' =>  $user->stakes()->sum('amount'),
                'Bookie' =>  $user->tickets()->sum('amount'),
                'Quota' =>   $user->level->settings('exchange_max_bet')
            ]
        ]);
    }


    /**
     * Update the specified user's details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'isBanned' => ['required', 'boolean'],
            'isKycVerified' => ['required', 'boolean'],
            'isPhoneVerified' => ['required', 'boolean'],
            'isEmailVerified' => ['required', 'boolean'],
            'isAddressVerified' => ['required', 'boolean'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $user->name  = $request->name;
        $user->email  =   $request->email;
        $user->phone  =    $request->phone;
        $user->banned_at  =   $request->isBanned ? now() : null;
        $user->kyc_verified_at  =    $request->isKycVerified ? now() : null;
        $user->phone_verified_at  =    $request->isPhoneVerified ? now() : null;
        $user->email_verified_at  =    $request->isEmailVerified ? now() : null;
        $user->address_verified_at  =    $request->isAddressVerified ? now() : null;
        $user->save();
        return redirect()->back()->with('success', __('User details updated successfully.'));
    }


    /**
     *  update users balance via manual transaction.
     *
     * @param \Illuminate\Http\Request $request
     * @param  \App\Model\User  $user
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function transact(Request $request, User $user)
    {
        $request->validate([
            'amount' => 'numeric|required|min:0',
            'details' => 'string|required',
            'type' => ['required', new Enum(TransactionAction::class)]
        ]);
        $balance_before = $user->balance;
        if ($request->type == 'credit') {
            $user->increment('balance', $request->amount);
        }
        if ($request->type == 'debit') {
            $user->decrement('balance', $request->amount);
        }
        $user->save();
        $user->transactions()->create([
            'user_id' => $user->id,
            'transactable_id' => $user->id,
            'transactable_type' => $user->getMorphClass(),
            'description' => $request->details,
            'amount' => $request->amount,
            'balance_before' => $balance_before,
            'action' => TransactionAction::from($request->type),
            'type' => TransactionType::ADMIN_ACTION
        ]);
        return back()->with('success', __('Transaction Executed successfully'));
    }

    /**
     * toggle status of  the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  \App\Model\User  $user
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
     * @param  \App\Model\User  $user
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function ban(Request $request, User $user)
    {
        $user->banned_at =  $user->banned_at ? null : now();
        $user->save();
        return back()->with('success', $user->banned_at ? __(' :name User Un banned !', ['name' => $user->name]) : __(' :name  User Banned!', ['name' => $user->name]));
    }



    /**
     * Disable two factor authentication for the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\User  $user
     * @param  \App\TwoFactorAuth\Actions\DisableTwoFactorAuthentication  $disable
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function twofactor(Request $request, User $user, DisableTwoFactorAuthentication  $disable)
    {
        $disable($user);
        return back()->with('success', __('Two factor authentication disabled for user'));
    }


    /**
     * Disable two factor authentication for the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\User  $user
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function verify(Request $request, User $user)
    {
        $request->validate(['type' => 'string|in:identity,address']);
        if ($request->type == 'identity')
            $user->kyc_verified_at  =    now();
        if ($request->type == 'address')
            $user->address_verified_at  =    now();
        $user->save();
        return back();
    }

    /**
     * toggle status of  the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  \App\Model\User  $user
     * @param int $level
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function level(Request $request, User $user, int $level)
    {
        $lvl = UserLevel::make($level);
        $fee = (float) $lvl->settings('optin_fees');
        if ($user->balance < $fee) return back()->with('error', 'Users Balance is Low');
        $user->decrement('balance', $fee);
        $user->transactions()->create([
            'user_id' => $user->id,
            'transactable_id' => $user->id,
            'transactable_type' => $user->getMorphClass(),
            'description' => "Admin  Upgrade to " . $lvl->name(),
            'amount' => $fee,
            'balance_before' => $user->balance + $fee,
            'action' => TransactionAction::DEBIT,
            'type' => TransactionType::LEVEL_UP
        ]);
        $user->level = UserLevel::make($level);
        $user->requested_next_level = false;
        $user->save();
        return back();
    }
}
