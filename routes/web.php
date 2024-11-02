<?php

use App\Http\Controllers\ConnectionsController;
use App\Http\Controllers\DepositsController;
use App\Http\Controllers\FavouritesController;
use App\Http\Controllers\GamesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\S3Controller;

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\LeaguesController;
use App\Http\Controllers\MarketsController;
use App\Http\Controllers\OddsController;
use App\Http\Controllers\PersonalsController;
use App\Http\Controllers\StakesController;
use App\Http\Controllers\TeamsController;
use App\Http\Controllers\TicketsController;
use App\Http\Controllers\TradesController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\WagersController;
use App\Http\Controllers\WhitelistsController;
use App\Http\Controllers\WithdrawsController;
use App\Http\Middleware\OnlyTimedOut;
use App\Http\Middleware\Timeout;
use App\Http\Resources\Personal;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::controller(S3Controller::class)
    ->group(function () {
        Route::post('sign/{disk?}/{folder?}', 'sign')->name('s3.sign');
    });

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::post('/multiples', function () {
    $multiples = session('multiples', false);
    session()->put('multiples', !$multiples);
    return back();
})->name('multiples');



Route::get('/privacy', function () {
    return Inertia::render('PrivacyPolicy', ['policy' => settings('pages.privacy')]);
})->name('privacy');

Route::get('/terms', function () {
    return Inertia::render('TermsOfService', ['terms' => settings('pages.terms')]);
})->name('terms');

Route::get('/timeout', function () {
    $user = Auth::user();
    return Inertia::render('TimeOut', ['personal' => new Personal($user->personal)]);
})->middleware(['auth', OnlyTimedOut::class])
    ->withoutMiddleware(Timeout::class)
    ->name('timeout');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [ProfileController::class, 'updateProfilePhoto'])->name('profile.update.photo');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';


#leagues
Route::name('leagues')->controller(LeaguesController::class)->group(function () {
    Route::get('/leagues', 'index')->name('index');
    Route::get('/leagues/{league}/show', 'show')->name('show');
});
#leagues


#teams
Route::name('teams')->controller(TeamsController::class)->group(function () {
    Route::get('/teams', 'index')->name('index');
    Route::get('/teams/{team}/show', 'show')->name('show');
});
#teams

#sports
Route::name('sports.')->controller(GamesController::class)->group(function () {
    Route::post('/sports/search', 'search')->name('search');
    Route::post('/sports/filter', 'filter')->name('filter');
    Route::get('/sport/watchlist', 'watchlist')
        ->middleware('auth')
        ->name('watchlist');
    Route::post('sport/add-watchlist/{game}', 'addToWatchlist')
        ->middleware('auth')
        ->name('watchlist.add');
    Route::delete('sport/remove-watchlist/{game}', 'removeFromWatchlist')
        ->middleware('auth')
        ->name('watchlist.remove');
    Route::get('/sport/in-play', 'inPlay')->name('inplay');
    Route::get('/sports/{sport?}/{region?}/{country?}', 'index')->name('index');
    Route::get('/sport/{game}/{slug?}', 'show')->name('show');
});
#sports

#markets
Route::name('markets')->controller(MarketsController::class)->group(function () {
    Route::get('/markets', 'index')->name('index');
    Route::get('/markets/{market}/show', 'show')->name('show');
});
#markets


#odds
Route::name('odds')->controller(OddsController::class)->group(function () {
    Route::get('/odds', 'index')->name('index');
    Route::get('/odds/{odd}/show', 'show')->name('show');
});
#odds

#stakes
Route::name('stakes.')
    ->controller(StakesController::class)
    ->middleware('auth')
    ->group(function () {
        Route::get('/stakes', 'index')->name('index');
        Route::get('/stake/tradeout/{stake:uid}', 'showTradeOut')->name('tradeout.show');
        Route::post('/stakes/store', 'store')->name('store');
        Route::put('/stakes/{stake}', 'tradeOut')->name('tradeout');
        Route::delete('/stakes/{stake}', 'cancel')->name('cancel');
    });
#stakes

#tickets
Route::name('tickets.')
    ->controller(TicketsController::class)
    ->middleware('auth')
    ->group(function () {
        Route::get('/tickets', 'index')->name('index');
        Route::post('/tickets/store', 'store')->name('store');
    });
#tickets

#wagers
Route::name('wagers')
    ->controller(WagersController::class)
    ->middleware('auth')
    ->group(function () {
        Route::get('/wagers', 'index')->name('index');
        Route::get('/wagers/create', 'create')->name('create');
        Route::post('/wagers/store', 'store')->name('store');
        Route::get('/wagers/{wager}/show', 'show')->name('show');
        Route::get('/wagers/{wager}/edit', 'edit')->name('edit');
        Route::put('/wagers/{wager}', 'update')->name('update');
        Route::put('/wagers/toggle/{wager}', 'toggle')->name('toggle');
        Route::delete('/wagers/{wager}', 'destroy')->name('destroy');
    });
#wagers
#trades
Route::name('trades')->controller(TradesController::class)->group(function () {
    Route::get('/trades', 'index')->name('index');
    Route::get('/trades/create', 'create')->name('create');
    Route::post('/trades/store', 'store')->name('store');
    Route::get('/trades/{trade}/show', 'show')->name('show');
    Route::get('/trades/{trade}/edit', 'edit')->name('edit');
    Route::put('/trades/{trade}', 'update')->name('update');
    Route::put('/trades/toggle/{trade}', 'toggle')->name('toggle');
    Route::delete('/trades/{trade}', 'destroy')->name('destroy');
});
#trades


#deposits
Route::name('deposits.')
    ->middleware('auth')
    ->controller(DepositsController::class)
    ->group(function () {
        Route::get('/deposits/create', 'create')->name('create');
        Route::post('/deposits/store', 'store')->name('store');
        Route::get('/deposit/{deposit:uuid}', 'show')->name('show');
        Route::put('/deposits/{deposit}', 'update')->name('update');
        Route::any('/deposits/cancel/{deposit}', 'cancel')->name('cancel');
        Route::any('/deposits/return/{deposit}', 'gatewayReturn')->name('return');
        Route::any('/webhooks/deposits/{provider}/{deposit}', 'webhooks')
            ->withoutMiddleware(['auth', ValidateCsrfToken::class])
            ->name('webhooks');
    });
#deposits

#withdraws
Route::name('withdraws.')
    ->middleware('auth')
    ->controller(WithdrawsController::class)
    ->group(function () {
        Route::get('/withdraws/create', 'create')->name('create');
        Route::post('/withdraws/store', 'store')->name('store');
        Route::get('/withdraw/{withdraw:uuid}', 'show')->name('show');
        Route::put('/withdraws/{withdraw}', 'confirm')->name('confirm');
        Route::delete('/withdraws/{withdraw}/cancel', 'cancel')->name('cancel');
    });
#withdraws

#connections
Route::name('connections.')->controller(ConnectionsController::class)->group(function () {
    Route::get('/connections', 'index')->middleware(['auth'])->name('index');
    Route::get('/connect/{provider}', 'create')->name('connect');
    Route::get('/callback/{provider}', 'create')->name('callback');
    Route::delete('/connections/{connection}', 'destroy')->middleware(['auth'])->name('destroy');
});
#connections


#favourites
Route::name('favourites.')
    ->controller(FavouritesController::class)
    ->middleware('auth')
    ->group(function () {
        Route::post('/favourites/store', 'store')->name('store');
        Route::post('/favourites/odds', 'odds')->name('odds');
        Route::post('/favourites/lang', 'lang')->name('lang');
        Route::post('/favourites/hide-balance', 'hideBalance')->name('hide.balance');
        Route::delete('/favourites/{favourite}', 'destroy')->name('destroy');
    });
#favourites



#transactions
Route::name('accounts.')
    ->prefix('account')
    ->controller(UsersController::class)
    ->middleware('auth')
    ->group(function () {
        Route::get('/statement', 'statement')->name('statement');
        Route::get('/verify', 'verify')->name('verify');
        Route::get('/settings', 'settings')->name('settings');
        Route::get('/settings/alerts', 'alerts')->name('alerts');
        Route::get('/settings/limits', 'limits')->name('limits');
        Route::get('/commission', 'commission')->name('commission');
        Route::get('/referrals', 'referrals')->name('referrals');
        Route::get('/promotions', 'promotions')->name('promotions');
        Route::post('/feedback', 'feedback')->name('feedback');
        Route::post('/optin', 'optin')->name('optin');
        Route::put('/verify/address', 'verifyAddress')->name('verify.address');
        Route::put('/verify/identity', 'verifyIdentity')->name('verify.identity');
        Route::put('/settings/personal', 'updatePersonal')->name('personal');
        Route::put('/settings/gross-limits', 'updateGrossLimits')->name('gross.limits');
    });

#transactions


#personal
Route::name('personal.')
    ->prefix('personal')
    ->controller(PersonalsController::class)
    ->middleware('auth')
    ->group(function () {
        Route::put('/personal', 'updatePersonal')->name('personal');
        Route::put('/personal/limit-deposits', 'limitDeposit')->name('limit.deposit');
        Route::put('/personal/limit-loss', 'limitLoss')->name('limit.loss');
        Route::put('/personal/limit-stake', 'limitStake')->name('limit.stake');
        Route::put('/personal/timeout', 'timeout')->name('timeout');
        Route::put('/personal/alerts', 'alerts')->name('alerts');
    });
#personal



#whitelists
Route::name('whitelists.')
    ->middleware('auth')
    ->controller(WhitelistsController::class)
    ->group(function () {
        Route::get('/whitelists', 'index')->name('index');
        Route::get('/whitelists/approve/{whitelist}/{approvalToken}', 'approve')->name('approve');
        Route::get('/whitelists/remove/{whitelist}/{removalToken}', 'completeRemoval')->name('complete.removal');
        Route::post('/whitelists/store', 'store')->name('store');
        Route::delete('/whitelists/{whitelist:uuid}', 'initiateRemoval')->name('destroy');
    });
#whitelists
