<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stake;
use App\Models\Game;
use App\Models\Bet;
use App\Models\User;
use App\Models\Trade;
use App\Enums\StakeType;
use App\Enums\StakeStatus;
use App\Models\Market;
use App\Support\TradeManager;
use Carbon\Carbon;
use Faker\Factory as Faker;

class StakesTestTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $game = Game::with(['markets'])->findOrFail(959);
        if ($game->markets->count() == 0) {
            $marketIds = Market::query()
                ->where('sport', $game->sport)
                ->where('active', true)
                ->pluck('id')
                ->all();
            $game->markets()->sync($marketIds);
        }
        $market = $game->markets()
            ->where('type', 'App\Enums\Soccer\Markets\GameResult')
            ->where('segment', 'fulltime')
            ->first();
        $bets = $market->bets;
        $user = User::first();
        $stakes = [];
        $tradedStakes = [];

        for ($i = 0; $i < 100; $i++) {
            $bet = $bets->random();
            $type = $faker->randomElement([StakeType::BACK, StakeType::LAY]);
            $amount = $faker->randomFloat(2, 10, 500);
            $odds = $type === StakeType::LAY
                ? $faker->randomFloat(2, 2.5, 3.5)
                : $faker->randomFloat(2, 1.5, 2.5);
            $stake = new Stake([
                'user_id' => $user->id,
                'bet_id' => $bet->id,
                'market_id' => $market->id,
                'game_id' => $game->id,
                'uid' => uniqid(),
                'amount' => $amount,
                'odds' => $odds,
                'type' => $type,
                'sport' =>  $game->sport,
                'status' => StakeStatus::PENDING,
                'unfilled' => $amount,
                'created_at' => Carbon::now()->subMinutes($faker->numberBetween(1, 60)),
                'updated_at' => Carbon::now(),
            ]);

            $stake->liability = TradeManager::calculateLiability($type, $amount, $odds);
            $stake->save();

            $stakes[] = $stake;

            // 20-40% chance of being traded
            if ($faker->boolean(10)) {
                $tradedStakes[] = $stake;
            }
        }
        // Process trades for selected stakes
        $tradedCount = count($tradedStakes);
        for ($i = 0; $i < min(20, $tradedCount); $i++) {
            $stake = $tradedStakes[$i];
            $oppositeType = $stake->type === StakeType::BACK ? StakeType::LAY : StakeType::BACK;
            $matchingStake = new Stake([
                'user_id' => $user->id,
                'bet_id' => $stake->bet_id,
                'market_id' => $stake->market_id,
                'game_id' => $stake->game_id,
                'uid' => uniqid(),
                'amount' => $stake->amount,
                'odds' => $stake->odds,
                'type' => $oppositeType,
                'sport' =>  $game->sport,
                'status' => StakeStatus::MATCHED,
                'unfilled' => 0,
                'filled' => $stake->amount,
                'created_at' => $stake->created_at->addSeconds($faker->numberBetween(1, 300)),
                'updated_at' => Carbon::now(),
            ]);
            $matchingStake->liability = TradeManager::calculateLiability($oppositeType, $matchingStake->amount, $matchingStake->odds);
            $matchingStake->save();
            $trade = new Trade([
                'maker_id' => $stake->id,
                'taker_id' => $matchingStake->id,
                'game_id' => $game->id,
                'bet_id' => $stake->bet_id,
                'market_id' => $stake->market_id,
                'amount' => $stake->amount,
                'price' => $stake->odds,
                'maker_price' => $stake->odds,
                'status' => 'pending',
                'created_at' => $matchingStake->created_at,
                'updated_at' => Carbon::now(),
            ]);
            $trade->save();
            $stake->status = StakeStatus::MATCHED;
            $stake->filled = $stake->amount;
            $stake->unfilled = 0;
            $stake->save();
        }

        $this->command->info('Stakes seeded successfully.');
    }
}
