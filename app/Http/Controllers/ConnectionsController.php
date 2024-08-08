<?php

namespace App\Http\Controllers;

use App\Enums\ConnectionProvider;
use App\Http\Controllers\Controller;
use App\Http\Resources\Connection as ConnectionResource;
use Inertia\Inertia;
use Exception;
use Google\Client;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Actions\Socialite as SocialiteAction;
use Arr;
use Laravel\Socialite\Two\User as Social;
use Str;

class ConnectionsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $connectionsItems = $request->user()->connections()->latest()->get();
        $connections = ConnectionResource::collection($connectionsItems);
        return Inertia::render('Connections/Index', compact('connections'));
    }

    /**
     * connect to a network
     * @return \Illuminate\View\View
     */
    public function connect(Request $request, ConnectionProvider $provider)
    {
        $url = Socialite::driver($provider->value)->redirect()->getTargetUrl();
        return Inertia::location($url);
    }

    /**
     * connect to a network
     * @return \Illuminate\View\View
     */
    public function callback(Request $request, ConnectionProvider $provider)
    {
        $socialUser = Socialite::driver($provider->value)->user();
        return app(SocialiteAction::class)
            ->connect($socialUser, $provider);
    }


    /**
     * connect to a network
     * @return \Illuminate\View\View
     */
    public function onetap(Request $request)
    {
        $socialUser = $this->getOneTapUser($request->credential);
        return app(SocialiteAction::class)
            ->connect($socialUser, ConnectionProvider::GOOGLE);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Request $request, $id)
    {
        $connection = $request->user()->connections()->findOrFail($id);
        $connection?->forceDelete();
        return redirect()->route('connections.index')->with('success', 'Connection deleted!');
    }

    /**
     * google onetap user
     */
    protected function getOneTapUser($token): Social
    {
        $client  = new Client([
            'client_id' => settings('google.client_id'),
            'client_secret' => config('google.client_secret'),
        ]);
        $info = $client->verifyIdToken($token);
        if (!$info) {
            throw new Exception('Invalid token');
        }
        return (new Social)
            ->setRaw($info)
            ->map([
                'id' => Arr::get($info, 'sub'),
                'nickname' => Str::of(Arr::get($info, 'name') . '-' . Str::random(8))->slug(),
                'name' => Arr::get($info, 'name'),
                'email' => Arr::get($info, 'email'),
                'avatar' => $avatarUrl = Arr::get($info, 'picture'),
                'avatar_original' => $avatarUrl,
                'given_name' => Arr::get($info, 'given_name'),
                'family_name' => Arr::get($info, 'family_name'),
            ])
            ->setToken($token);
    }
}
