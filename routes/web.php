<?php

use App\Http\Controllers\ConnectionsController;
use App\Http\Controllers\FavouritesController;
use App\Http\Controllers\GamesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\S3Controller;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\SportsController;
use App\Http\Controllers\LeaguesController;
use App\Http\Controllers\MarketsController;
use App\Http\Controllers\OddsController;
use App\Http\Controllers\StakesController;
use App\Http\Controllers\TeamsController;
use App\Http\Controllers\TicketsController;
use App\Http\Controllers\TradesController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\WagersController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home');
Route::controller(S3Controller::class)
    ->group(function () {
        Route::post('sign/{disk?}/{folder?}', 'sign')->name('s3.sign');
    });

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/privacy', function () {
    return Inertia::render('PrivacyPolicy', ['policy' => settings('pages.privacy')]);
})->name('privacy');
Route::get('/terms', function () {
    return Inertia::render('TermsOfService', ['terms' => settings('pages.terms')]);
})->name('terms');


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
    Route::get('/sport/watchlist', 'watchlist')->name('watchlist');
    Route::get('/sport/in-play', 'inPlay')->name('inplay');
    Route::get('/sports/{sport?}/{region?}/{country?}', 'index')->name('index');
    Route::get('/sport/{game}', 'show')->name('show');
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
Route::name('stakes')->controller(StakesController::class)->group(function () {
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
Route::name('tickets')->controller(TicketsController::class)->group(function () {
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
Route::name('wagers')->controller(WagersController::class)->group(function () {
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



#transactions
Route::name('transactions')->controller(TransactionsController::class)->group(function () {
    Route::get('/transactions', 'index')->name('index');
    Route::get('/transactions/create', 'create')->name('create');
    Route::post('/transactions/store', 'store')->name('store');
    Route::get('/transactions/{transaction}/show', 'show')->name('show');
    Route::get('/transactions/{transaction}/edit', 'edit')->name('edit');
    Route::put('/transactions/{transaction}', 'update')->name('update');
    Route::put('/transactions/toggle/{transaction}', 'toggle')->name('toggle');
    Route::delete('/transactions/{transaction}', 'destroy')->name('destroy');
});
#transactions


#accounts
Route::name('accounts')->controller(AccountsController::class)->group(function () {
    Route::get('/accounts', 'index')->name('index');
    Route::get('/accounts/create', 'create')->name('create');
    Route::post('/accounts/store', 'store')->name('store');
    Route::get('/accounts/{account}/show', 'show')->name('show');
    Route::get('/accounts/{account}/edit', 'edit')->name('edit');
    Route::put('/accounts/{account}', 'update')->name('update');
    Route::put('/accounts/toggle/{account}', 'toggle')->name('toggle');
    Route::delete('/accounts/{account}', 'destroy')->name('destroy');
});
#accounts


#commissions
Route::name('commissions')->controller(CommissionsController::class)->group(function () {
    Route::get('/commissions', 'index')->name('index');
    Route::get('/commissions/create', 'create')->name('create');
    Route::post('/commissions/store', 'store')->name('store');
    Route::get('/commissions/{commission}/show', 'show')->name('show');
    Route::get('/commissions/{commission}/edit', 'edit')->name('edit');
    Route::put('/commissions/{commission}', 'update')->name('update');
    Route::put('/commissions/toggle/{commission}', 'toggle')->name('toggle');
    Route::delete('/commissions/{commission}', 'destroy')->name('destroy');
});
#commissions


#deposits
Route::name('deposits')->controller(DepositsController::class)->group(function () {
    Route::get('/deposits', 'index')->name('index');
    Route::get('/deposits/create', 'create')->name('create');
    Route::post('/deposits/store', 'store')->name('store');
    Route::get('/deposits/{deposit}/show', 'show')->name('show');
    Route::get('/deposits/{deposit}/edit', 'edit')->name('edit');
    Route::put('/deposits/{deposit}', 'update')->name('update');
    Route::put('/deposits/toggle/{deposit}', 'toggle')->name('toggle');
    Route::delete('/deposits/{deposit}', 'destroy')->name('destroy');
});
#deposits


#payouts
Route::name('payouts')->controller(PayoutsController::class)->group(function () {
    Route::get('/payouts', 'index')->name('index');
    Route::get('/payouts/create', 'create')->name('create');
    Route::post('/payouts/store', 'store')->name('store');
    Route::get('/payouts/{payout}/show', 'show')->name('show');
    Route::get('/payouts/{payout}/edit', 'edit')->name('edit');
    Route::put('/payouts/{payout}', 'update')->name('update');
    Route::put('/payouts/toggle/{payout}', 'toggle')->name('toggle');
    Route::delete('/payouts/{payout}', 'destroy')->name('destroy');
});
#payouts


#withdraws
Route::name('withdraws')->controller(WithdrawsController::class)->group(function () {
    Route::get('/withdraws', 'index')->name('index');
    Route::get('/withdraws/create', 'create')->name('create');
    Route::post('/withdraws/store', 'store')->name('store');
    Route::get('/withdraws/{withdraw}/show', 'show')->name('show');
    Route::get('/withdraws/{withdraw}/edit', 'edit')->name('edit');
    Route::put('/withdraws/{withdraw}', 'update')->name('update');
    Route::put('/withdraws/toggle/{withdraw}', 'toggle')->name('toggle');
    Route::delete('/withdraws/{withdraw}', 'destroy')->name('destroy');
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
        Route::delete('/favourites/{favourite}', 'destroy')->name('destroy');
    });
#favourites