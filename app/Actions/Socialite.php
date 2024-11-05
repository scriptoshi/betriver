<?php

namespace App\Actions;

use App\Models\Connection;
use App\Actions\Random;
use App\Enums\ConnectionProvider;
use App\Models\User;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as Social;
use Storage;

class Socialite
{

    /**
     * connect to a network
     * @return \Illuminate\View\View
     */
    /**
     * connect to a network
     * @return \Illuminate\View\View
     */
    public function connect(Social $socialUser, ConnectionProvider $provider)
    {
        $connection = Connection::with('user')
            ->where('userId', $socialUser->getId())
            ->where('provider', $provider)
            ->first();
        if ($connection) {
            Auth::login($connection->user);
            return redirect()->intended(route('dashboard', absolute: false));
        }
        if ($user = User::where('email', $socialUser->getEmail())->exists()) {
            $this->createConnection($user, $socialUser, $provider);
            Auth::login($user);
            return redirect()->intended(route('dashboard', absolute: false));
        }
        $user = $this->create($socialUser, $provider);
        Auth::login($user);
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(Social $social, ConnectionProvider $provider): User
    {
        $upline = null;
        if ($ref = request(null)->cookie('referral')) {
            $refferal = User::where('refId', $ref)->first();
            if ($refferal) $upline =  $refferal->referral ? $refferal->refId . ':' . $refferal->referral : $refferal->refId;
        }
        $user =  DB::transaction(function () use ($social, $provider, $upline) {
            return tap(User::create([
                'name' => $social->getNickname(),
                'email' => $social->getEmail(),
                'password' => Hash::make(Str::random(20)),
                'refId' => Random::generate(10),
                'referral' =>  $upline,
            ]), function (User $user) use ($social, $provider) {
                $user->email_verified_at = now();
                $user->save();
                $this->createConnection($user, $social, $provider);
            });
        });
        if ($social->getAvatar())
            $this->saveProfilePhoto($user, $social->getAvatar());
        //$user->setDefaultCountry();
        return $user;
    }


    /**
     * Create a personal team for the user.
     */
    public function createConnection(User $user, Social $social, ConnectionProvider $provider): void
    {
        $user->connections()->updateOrCreate([
            'userId' =>  $social->getId(),
            'provider' =>  $provider
        ], []);
    }

    protected function saveProfilePhoto($user, $url)
    {
        $file = file_get_contents($url);
        $path = 'profile-photos/' . Str::random(20) . '.jpg';
        $disk = settings('profile_photo_disk', 'public');
        if (Storage::disk($disk)->put($path, $file, 'public')) {
            $user->profile_photo_path = $path;
            $user->save();
        }
    }
}
