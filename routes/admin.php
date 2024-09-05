<?php


use App\Http\Controllers\Admin\CommissionsController;
use App\Http\Controllers\Admin\DepositsController;
use App\Http\Controllers\Admin\FeedbackController;
use App\Http\Controllers\Admin\GamesController;
use App\Http\Controllers\Admin\LeaguesController;
use App\Http\Controllers\Admin\MarketsController;
use App\Http\Controllers\Admin\OddsController;
use App\Http\Controllers\Admin\ScoresController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SlidersController;
use App\Http\Controllers\Admin\StakesController;
use App\Http\Controllers\Admin\TeamsController;
use App\Http\Controllers\Admin\TicketsController;
use App\Http\Controllers\Admin\TradesController;
use App\Http\Controllers\Admin\TransactionsController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\WagersController;
use App\Http\Controllers\Admin\WhitelistsController;
use App\Http\Controllers\Admin\WithdrawsController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Admin/Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

#users
Route::name('users.')->controller(UsersController::class)->group(function () {
    Route::get('/user/{user}', 'show')->name('show');
    Route::get('/users/{filter?}', 'index')->name('index');
    Route::put('/users/toggle/{user}', 'toggle')->name('toggle');
    Route::put('/users/ban/{user}', 'ban')->name('ban');
    Route::put('/users/update/{user}', 'update')->name('update');
    Route::put('/users/transact/{user}', 'transact')->name('transact');
    Route::put('/users/level/{user}/{level}', 'level')->name('level');
    Route::put('/users/verify/{user}', 'verify')->name('verify');
    Route::delete('/users/twofactor/{user}', 'twofactor')->name('twofactor');
});

#commissions
Route::name('commissions.')->controller(CommissionsController::class)->group(function () {
    Route::get('/commissions/{type?}', 'index')->name('index');
    Route::get('/commissions/create/{type}/{num?}', 'create')->name('create');
    Route::post('/commissions/store', 'store')->name('store');
    Route::get('/commissions/{commission}/show', 'show')->name('show');
    Route::get('/commissions/{commission}/edit', 'edit')->name('edit');
    Route::put('/commissions/{commission}', 'update')->name('update');
    Route::put('/commissions/toggle/{commission}', 'toggle')->name('toggle');
    Route::delete('/commissions/destroy/{commission}', 'destroy')->name('destroy');
});
#commissions

#leagues
Route::name('leagues.')->controller(LeaguesController::class)->group(function () {
    Route::get('/leagues/create/{sport?}', 'create')->name('create');
    Route::get('/leagues/{sport?}', 'index')->name('index');
    Route::post('/leagues/store', 'store')->name('store');
    Route::post('/leagues/pull', 'pull')->name('pull');
    Route::get('/leagues/{league}/show', 'show')->name('show');
    Route::get('/leagues/{league}/edit', 'edit')->name('edit');
    Route::put('/league/load-odds/{league}', 'loadOddsFromApi')->name('load.odds');
    Route::put('/leagues/enable', 'enableAll')->name('enable');
    Route::put('/leagues/disable', 'disableAll')->name('disable');
    Route::put('/leagues/{league}', 'update')->name('update');
    Route::put('/leagues/toggle/{league}', 'toggle')->name('toggle');
    Route::put('/leagues/menu/{league}', 'toggleMenu')->name('menu');
    Route::delete('/leagues/{league}', 'destroy')->name('destroy');
});
#leagues

#teams
Route::name('teams.')->controller(TeamsController::class)->group(function () {
    Route::get('/teams/create/{sport?}', 'create')->name('create');
    Route::get('/teams/{sport?}', 'index')->name('index');
    Route::post('/teams/store', 'store')->name('store');
    Route::get('/teams/{team}/show', 'show')->name('show');
    Route::get('/teams/{team}/edit', 'edit')->name('edit');
    Route::put('/teams/{team}', 'update')->name('update');
    Route::put('/teams/toggle/{team}', 'toggle')->name('toggle');
    Route::delete('/teams/{team}', 'destroy')->name('destroy');
});
#teams


#games
Route::name('games.')->controller(GamesController::class)->group(function () {
    Route::get('/games/create/{sport?}', 'create')->name('create');
    Route::get('/games/{sport?}', 'index')->name('index');
    Route::post('/games/store', 'store')->name('store');
    Route::get('/games/{game}/show', 'show')->name('show');
    Route::get('/games/{game}/edit', 'edit')->name('edit');
    Route::put('/games/load', 'loadFromApi')->name('load');
    Route::put('/games/{game}', 'update')->name('update');
    Route::put('/games/toggle/{game}', 'toggle')->name('toggle');
    Route::delete('/games/{game}', 'destroy')->name('destroy');
});
#games


#scores
Route::name('scores.')->controller(ScoresController::class)->group(function () {
    Route::get('/scores/{game:uuid}', 'index')->name('index');
    Route::post('/scores/store', 'store')->name('store');
    Route::post('/scores/mma', 'mma')->name('mma');
});
#scores


#markets
Route::name('markets.')->controller(MarketsController::class)->group(function () {
    Route::get('/markets/{sport?}', 'index')->name('index');
    Route::post('/markets/reorder', 'reorder')->name('reorder');
    Route::post('/markets/sequence', 'sequence')->name('sequence');
    Route::post('/markets/toggle/{market}', 'toggle')->name('toggle');
    Route::put('/markets/toggle-bookie/{market}', 'toggleBookie')->name('toggle.bookie');
});
#markets


#odds
Route::name('odds.')->controller(OddsController::class)->group(function () {
    Route::get('/odds/{game:uuid}', 'index')->name('index');
    Route::post('/odds/store', 'store')->name('store');
    Route::put('/odds/toggle/bookie/{gameMarket}', 'toggleBookie')->name('toggle.bookie');
    Route::put('/odds/toggle/{gameMarket}', 'toggle')->name('toggle');
});
#odds
#stakes
Route::name('stakes.')->controller(StakesController::class)->group(function () {
    Route::get('/stakes', 'index')->name('index');
    Route::get('/stakes/create', 'create')->name('create');
    Route::post('/stakes/store', 'store')->name('store');
    Route::get('/stakes/{stake}/show', 'show')->name('show');
    Route::get('/stakes/{stake}/edit', 'edit')->name('edit');
    Route::put('/stakes/{stake}', 'update')->name('update');
    Route::put('/stakes/toggle/{stake}', 'toggle')->name('toggle');
    Route::delete('/stakes/{stake}', 'destroy')->name('destroy');
});
#stakes
#tickets
Route::name('tickets.')->controller(TicketsController::class)->group(function () {
    Route::get('/tickets', 'index')->name('index');
    Route::get('/tickets/create', 'create')->name('create');
    Route::post('/tickets/store', 'store')->name('store');
    Route::get('/tickets/{ticket}/show', 'show')->name('show');
    Route::get('/tickets/{ticket}/edit', 'edit')->name('edit');
    Route::put('/tickets/{ticket}', 'update')->name('update');
    Route::put('/tickets/toggle/{ticket}', 'toggle')->name('toggle');
    Route::delete('/tickets/{ticket}', 'destroy')->name('destroy');
});
#tickets
#wagers
Route::name('wagers.')->controller(WagersController::class)->group(function () {
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
Route::name('trades.')->controller(TradesController::class)->group(function () {
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

#transactions
Route::name('transactions.')->controller(TransactionsController::class)->group(function () {
    Route::get('/transactions', 'index')->name('index');
    Route::put('/transactions/{transaction:uuid}', 'reverse')->name('reverse');
    Route::delete('/transactions/{transaction:uuid}', 'destroy')->name('destroy');
});

Route::name('deposits.')->controller(DepositsController::class)->group(function () {
    Route::get('/deposits', 'index')->name('index');
    Route::put('/deposits/{deposit:uuid}', 'complete')->name('complete');
    Route::delete('/deposits/{deposit:uuid}', 'fail')->name('fail');
});

Route::name('withdraws.')->controller(WithdrawsController::class)->group(function () {
    Route::get('/withdraws', 'index')->name('index');
    Route::put('/withdraws/status/{withdraw:uuid}', 'status')->name('status');
    Route::put('/withdraws/refresh/{withdraw:uuid}', 'refresh')->name('refresh');
    Route::post('/withdraws/twofa/{withdraw:uuid}', 'twofa')->name('twofa');
    Route::put('/withdraws/batch', 'batch')->name('batch');
    Route::put('/withdraws/{withdraw:uuid}', 'complete')->name('complete');
    Route::delete('/withdraws/{withdraw:uuid}', 'fail')->name('fail');
});
#transactions

#settings
Route::name('settings.')->controller(SettingsController::class)->group(function () {
    Route::get('/settings', 'index')->name('index');
    Route::get('/settings/site', 'site')->name('site');
    Route::get('/settings/social', 'social')->name('social');
    Route::get('/settings/meta', 'meta')->name('meta');
    Route::get('/settings/privacy-and-terms', 'privacyTerms')->name('privacy');
    Route::get('/settings/notifications', 'notifications')->name('notifications');
    Route::get('/settings/payments', 'payments')->name('payments');
    Route::get('/settings/levels', 'levels')->name('levels');
    Route::get('/settings/create', 'create')->name('create');
    Route::post('/settings/store', 'store')->name('store');
    Route::post('/settings/social/store', 'storeSocial')->name('social.store');
    Route::post('/settings/meta/store', 'storeMeta')->name('meta.store');
    Route::post('/settings/privacy-and-terms/store', 'storePrivacyTerms')->name('privacy.store');
    Route::post('/settings/notifications/store', 'storeNotifications')->name('notifications.store');
    Route::post('/settings/payments/store', 'storePayments')->name('payments.store');
    Route::post('/settings/mail/store', 'storeMail')->name('mail.store');
    Route::post('/settings/sms/store', 'storeSMS')->name('sms.store');
    Route::post('/settings/messages/store', 'storeMessages')->name('messages.store');
    Route::post('/settings/levels/store/{level}', 'storeLevels')->name('levels.store');
    Route::get('/settings/{setting}/show', 'show')->name('show');
    Route::get('/settings/{setting}/edit', 'edit')->name('edit');
    Route::put('/settings/{setting}', 'update')->name('update');
    Route::put('/settings/toggle/{setting}', 'toggle')->name('toggle');
    Route::delete('/settings/{setting}', 'destroy')->name('destroy');
});
#settings

#settings
Route::name('sitemap.')->controller(SettingsController::class)->group(function () {
    Route::post('/sitemap', 'generateSitemap')->name('generate');
    Route::delete('/sitemap/destroy', 'destroySitemap')->name('destroy');
});
#settings


#feedback
Route::name('feedback.')->controller(FeedbackController::class)->group(function () {
    Route::get('/feedback', 'index')->name('index');
    Route::delete('/feedback/{feedback}', 'destroy')->name('destroy');
});
#feedback

Route::name('whitelists.')
    ->middleware('auth')
    ->controller(WhitelistsController::class)
    ->group(function () {
        Route::get('/whitelists', 'index')->name('index');
        Route::get('/whitelists/download', 'download')->name('download');
        Route::post('/whitelists/approve/{whitelist}', 'approve')->name('approve');
        Route::post('/whitelists/mass/approve', 'massApprove')->name('mass.approve');
        Route::post('/whitelists/mass/reject', 'massReject')->name('mass.reject');
    });

#sliders
Route::name('sliders.')->controller(SlidersController::class)->group(function () {
    Route::get('/sliders', 'index')->name('index');
    Route::get('/sliders/create', 'create')->name('create');
    Route::post('/sliders/store', 'store')->name('store');
    Route::get('/sliders/{slider}/edit', 'edit')->name('edit');
    Route::put('/sliders/{slider}', 'update')->name('update');
    Route::put('/sliders/toggle/{slider}', 'toggle')->name('toggle');
    Route::delete('/sliders/{slider}', 'destroy')->name('destroy');
});
#sliders
