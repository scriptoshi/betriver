<?php

namespace App\Http\Middleware;

use App\Enums\LeagueSport;
use App\Enums\StakeStatus;
use App\Http\Resources\User;
use App\Models\Game;
use App\Models\League;
use App\Models\Personal;
use App\Support\LeagueSlug;
use App\Support\TradeManager;
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
        return [
            ...parent::share($request),
            'multiples' => fn() => $request->session()->get('multiples', false),
            'auth' => function () use ($user) {
                if ($user) {
                    $personal =  $user->personal()->firstOrCreate([
                        'user_id' => $user->id
                    ], []);
                    $user->load([
                        'stakes' => fn($q) => $q->with('game')->latest()->limit(5),
                        'tickets' => fn($q) => $q->with('wagers.game')->latest()->limit(5)
                    ]);
                }
                return [
                    'user' =>  $user ? new User($user) : null,
                    'personal' =>  $user ? $personal : null,
                    'watchlist' => $user ? $user->watchlist()->pluck('games.id')->all() : [],
                ];
            },
            'isAdmin' => fn() => $user ? $user->isAdmin() : false,
            'flash' => [
                'message' => fn() => $request->session()->get('message'),
                'error' => fn() => $request->session()->get('error'),
                'success' => fn() => $request->session()->get('success'),
                'info' => fn() => $request->session()->get('info'),
            ],
            'exposure' => function () use ($user) {
                if (!$user) return  0;
                return $user->stakes()->whereIn('status', StakeStatus::exposed())->sum('liability');
            },
            'potentialWinnings' => function () use ($user) {
                if (!$user) return  0;
                return $user->stakes()->whereIn('status', StakeStatus::exposed())->sum('payout');
            },
            'appName' => fn() => settings('site.app_name'),
            'appLogo' => fn() => settings('site.logo'),
            'appDescription' => fn() => settings('site.description'),
            'uploadsDisk' => fn() => settings('site.uploads_disk'),
            's3' => fn() => settings('site.uploads_disk') != 'public',
            'profilePhotoDisk' => fn() => settings('site.profile_photo_disk'),
            'twoFactorRequiresConfirmation' => fn() => settings('twofa.confirm'),
            'googleClientId' => fn() => settings('google.client_id'),
            'enableGoogleLogin' => fn() => !!settings('google.client_id', null) && str(settings('google.enable'))->toBoolean(),
            'enableGithubLogin' => fn() => !!settings('github.client_id', null) &&  str(settings('github.enable'))->toBoolean(),
            'enableFacebookLogin' => fn() => !!settings('facebook.client_id', null) && str(settings('facebook.enable'))->toBoolean(),
            'gdprTitle' => fn() => settings('pages.gdpr_notice'),
            'gdprText' => fn() => settings('pages.gdpr_terms'),
            'enableKyc' => fn() => str(settings('site.enable_kyc'))->toBoolean(),
            'currency' => fn() => [
                'currency_code' => settings('site.currency_code'),
                'currency_symbol' => settings('site.currency_symbol'),
                'currency_display' => settings('site.currency_display'),
            ],
            'sports' => fn() => collect(LeagueSport::cases())->map(fn(LeagueSport $l) => strlen($l->value) == 3 ? strtoupper($l->value) :  ucfirst($l->value)),
            'menus' => function () {
                $activeCounts = Game::getCountsBySport();
                $leagues = League::getLeaguesBySport();
                $footballLeagues = League::getLeaguesByCountry(LeagueSport::FOOTBALL);
                return collect(LeagueSport::cases())->map(function (LeagueSport $sport) use ($activeCounts, $leagues, $footballLeagues) {
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
                            'next_week' => $activeCounts[$sport->value]['next_week'] ?? 0,
                            'ended' => $activeCounts[$sport->value]['ended'] ?? 0,
                            'leagues' => $leaguesMenu
                        ],
                    ];
                });
            },
            'quicklinks' => function () use ($user) {
                $links = ['watchlist', 'inplay'];
                if (Auth::check()) {
                    $favourites = $user->favourites()->pluck('key')->values()->all();
                    $links = [...$links, ...$favourites];
                }
                return $links;
            },
            'liveCount' => fn() => Game::getLiveCount(),
            'popular' => fn() => TradeManager::popular(),
        ];
    }
}
