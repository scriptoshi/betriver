<?php


use App\Api\ApiMma;
use App\Enums\LeagueSport;
use App\Enums\WithdrawGateway;
use App\Enums\WithdrawStatus;
use App\Gateways\Payment\Drivers\CoinPayments;
use App\Gateways\Payment\Drivers\NowPayments;
use App\Models\Deposit;
use App\Models\Game;
use App\Models\Withdraw;
use App\Support\OverroundCalculator;
use App\Support\Rate;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('update:rates', function () {
    Rate::update();
})->daily();

// update games that are live.  Api cant batch live games
// so we load all games today.
Artisan::command('update:games', function () {
    collect(LeagueSport::cases())->each(function (LeagueSport $sport) {
        if ($sport == LeagueSport::RACING) return; // no api for races etx
        if (Game::where('sport', $sport->value)->live()->count() == 0)  return;
        $sport->api()::loadGames(0, 1);
    });
})->hourly();

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
})->daily();


Artisan::command('lang:strap', function () {
    Artisan::call('translatable:export', ['lang' => 'en']);
    Artisan::call('vue-i18n:generate', ['--with-vendor' => 'en']);
});

Artisan::command('nwp', function () {
    $x =  LeagueSport::FOOTBALL->handicaps();
    dd($x);
});

Artisan::command('txt', function () {
    // Example usage:
    $backOdds = [2.00, 3.50, 3.50]; // Example odds for a three-way market
    $overround = OverroundCalculator::calculateOverround($backOdds);
    $fairOdds = OverroundCalculator::calculateFairOdds($backOdds);

    echo "Market overround: " . $overround . "%\n";
    echo "Fair odds: " . implode(", ", $fairOdds) . "\n";
});







Artisan::command('bo', function () {
    ApiMma::updateLeagues();
});
