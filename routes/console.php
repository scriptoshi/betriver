<?php

use App\Api\ApiAfl;
use App\Api\ApiBaseball;
use App\Api\ApiBasketball;
use App\Api\ApiFootball;
use App\Api\ApiHandball;
use App\Api\ApiHokey;
use App\Api\ApiMma;
use App\Api\ApiNfl;
use App\Api\ApiRugby;
use App\Api\ApiVolleyball;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


Artisan::command('lang:strap', function () {
    Artisan::call('translatable:export', ['lang' => 'en']);
    Artisan::call('vue-i18n:generate', ['--with-vendor' => 'en']);
});

Artisan::command('settle:trades', function () {
   
});






Artisan::command('bo', function () {
    ApiMma::updateLeagues();
});
