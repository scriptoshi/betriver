<?php

namespace Database\Seeders;

use App\Contracts\BetMarket;
use App\Enums\Market;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use function Clue\StreamFilter\fun;

class MarketsTwoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeds the market and bets table from list of implemented markets
     */
    public function run(): void
    {
        $markets = collect(Market::seedGroupTwo());
        $markets->each(function (Market $market) {
            $market->initialize()->each(fn (BetMarket $bm) => $bm->seed());
        });
    }
}
