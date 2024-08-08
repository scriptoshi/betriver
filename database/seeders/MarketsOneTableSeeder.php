<?php

namespace Database\Seeders;

use App\Contracts\BetMarket;
use App\Enums\Market;
use Illuminate\Database\Seeder;



class MarketsOneTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeds the market and bets table from list of implemented markets
     */
    public function run(): void
    {
        $markets = collect(Market::seedGroupOne());
        $markets->each(function (Market $market) {
            $market->initialize()->each(fn (BetMarket $bm) => $bm->seed());
        });
    }
}
