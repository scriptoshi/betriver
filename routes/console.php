<?php

use App\Actions\SettleStakes;
use App\Api\ApiMma;
use App\Enums\LeagueSport;
use App\Enums\WithdrawGateway;
use App\Enums\WithdrawStatus;
use App\Models\Game;
use App\Models\GameMarket;
use App\Models\Withdraw;
use App\Support\OverroundCalculator;
use App\Support\Rate;
use Illuminate\Support\Facades\Artisan;


// update currency rates
Artisan::command('update:rates', function () {
    Rate::update();
})->purpose('update currency rates')
    ->daily();

// update games that are live.  Api cant batch live games
// so we load all games today.
Artisan::command('update:games', function () {
    collect(LeagueSport::cases())->each(function (LeagueSport $sport) {
        if ($sport == LeagueSport::RACING) return; // no api for races etx
        if (Game::where('sport', $sport->value)->live()->count() == 0)  return;
        $sport->api()::loadGames(0, 1);
    });
})
    ->purpose('update games that are live')
    ->everyFiveMinutes();

/**
 * Updates the processing withdraw status at the gateway
 */
Artisan::command('withdraws:update', function () {
    $withdraws = Withdraw::query()
        ->where('status', WithdrawStatus::PROCESSING)
        ->where('gateway', '!=', WithdrawGateway::PAYPAL)
        ->get();
    $withdraws->map(fn(Withdraw $withdraw) => $withdraw->gateway->driver()->updateWithdrawStatus($withdraw));
    $paypal = Withdraw::query()
        ->where('status', WithdrawStatus::PROCESSING)
        ->where('gateway',  WithdrawGateway::PAYPAL)
        ->distinct('batchId')
        ->get();
    $paypal->map(fn(Withdraw $withdraw) => $withdraw->gateway->driver()->updateWithdrawStatus($withdraw));
})
    ->purpose('Update the processing withdraw status at the gateway')
    ->everyFifteenMinutes();


/**
 * Settle stakes on the system
 */
Artisan::command('settle:stakes', function () {
    app(SettleStakes::class)->execute();
})
    ->purpose('Settle Stakes on the System')
    ->everyFiveMinutes();



Artisan::command('lang:strap', function () {
    Artisan::call('translatable:export', ['lang' => 'en']);
    Artisan::call('vue-i18n:generate', ['--with-vendor' => 'en']);
});
