<?php

namespace App\Http\Middleware;

use App\Enums\LeagueSport;
use App\Http\Resources\User;
use App\Models\Game;
use App\Models\League;
use App\Support\LeagueSlug;
use Auth;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Storage;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        $activeCounts = Game::getCountsBySport();
        $leagues = League::getLeaguesBySport();
        $footballLeagues = League::getLeaguesByCountry(LeagueSport::FOOTBALL);
        return [
            ...parent::share($request),
            'auth' => [
                'user' =>  $user ? new User($user) : null,
            ],
            'isAdmin' => $user ? $user->isAdmin() : false,
            'flash' => [
                'message' => fn() => $request->session()->get('message'),
                'error' => fn() => $request->session()->get('error'),
                'success' => fn() => $request->session()->get('success'),
                'info' => fn() => $request->session()->get('info'),
            ],
            'appName' => settings('site.app_name'),
            'appLogo' => settings('site.logo'),
            'appDescription' => settings('site.description'),
            'uploadsDisk' => settings('site.uploads_disk'),
            's3' => settings('site.uploads_disk') != 'public',
            'profilePhotoDisk' => settings('site.profile_photo_disk'),
            'twoFactorRequiresConfirmation' => settings('twofa.confirm'),
            'googleClientId' => settings('google.client_id'),
            'enableGoogleLogin' => !!settings('google.client_id', null) && str(settings('google.enable'))->toBoolean(),
            'enableGithubLogin' => !!settings('github.client_id', null) &&  str(settings('github.enable'))->toBoolean(),
            'enableFacebookLogin' => !!settings('facebook.client_id', null) && str(settings('facebook.enable'))->toBoolean(),
            'gdprTitle' => settings('pages.gdpr_notice'),
            'gdprText' => settings('pages.gdpr_terms'),
            'sports' => collect(LeagueSport::cases())->map(fn(LeagueSport $l) => strlen($l->value) == 3 ? strtoupper($l->value) :  ucfirst($l->value)),
            'menus' => collect(LeagueSport::cases())->map(function (LeagueSport $sport) use ($activeCounts, $leagues, $footballLeagues) {
                $leaguesMenu = match ($sport) {
                    LeagueSport::FOOTBALL => $footballLeagues,
                    default => collect($leagues[$sport->value] ?? [])->keyBy('slug')->all(),
                };
                return [
                    'route' => "sports.index",
                    'sport' => $sport->value,
                    'count' =>  $activeCounts[$sport->value]['total'] ?? 0,
                    'submenu' =>  [
                        'live' =>  $activeCounts[$sport->value]['live'] ?? 0,
                        'today' => $activeCounts[$sport->value]['today'] ?? 0,
                        'tomorrow' => $activeCounts[$sport->value]['tomorrow'] ?? 0,
                        'this_week' => $activeCounts[$sport->value]['this_week'] ?? 0,
                        'ended' => $activeCounts[$sport->value]['ended'] ?? 0,
                        'leagues' => $leaguesMenu
                    ],
                ];
            }),
            'quicklinks' => function () use ($user) {
                $links = ['watchlist', 'inplay'];
                if (Auth::check()) {
                    $favourites = $user->favourites()->pluck('key')->values()->all();
                    $links = [...$links, ...$favourites];
                }
                return $links;
            },
            'liveCount' => Game::getLiveCount()
        ];
    }
}
