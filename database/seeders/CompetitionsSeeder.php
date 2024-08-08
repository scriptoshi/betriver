<?php

namespace Database\Seeders;

use App\Models\Market;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompetitionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Market::create([
            'name' => 'Competitions',
            'description' => 'Multi Player/Team Competitions',
            'slug' => 'competitions',
            'segment' => 'fulltime',
            'sport' => 'competition',
            'oddsId' => '4',
            'type' => 'App\\Enums\\Soccer\\Markets\\AsianHandicap',
            'active' => 1,
            'bookie_active' => 1,
            'created_at' => '2024-08-07 19:06:26',
            'updated_at' => '2024-08-07 19:06:26',
            'deleted_at' => NULL
        ]);
    }
}
