<?php

namespace Database\Seeders;

use App\Actions\Random;
use App\Models\User;
use Hash;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user = User::query()->create([
            'name' => 'Admin',
            'is_admin' => true,
            'password' => Hash::make('*Codex123#'),
            'email' => 'admin@coderiver.io',
            'refId' => Random::generate()
        ]);
        $user->personal()->create([]);
        $this->call(SettingsTableSeeder::class);
        //$this->call(MarketsTableSeeder::class);
        //$this->call(BetsTableSeeder::class);
        /**
         * Adjust these seeders to seed the markets afresh
         * using the Markets ENUM.  
         * Ensure to delete the MarketsTableSeeder and BetsTableSeeder above before
         */
        $this->call(MarketsOneTableSeeder::class);
        $this->call(MarketsTwoTableSeeder::class);
    }
}
