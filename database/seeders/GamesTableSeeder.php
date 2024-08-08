<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class GamesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('games')->delete();
        \DB::table('games')->insert([
            'id' => 1,
            'uuid' => '1de1cb2a-be53-49f1-8b1f-5b4c6c96f9cd',
            'league_id' => 1,
            'home_team_id' => 1,
            'away_team_id' => 2,
            'name' => 'Manchester United vs Arsenl FC',
            'gameId' => NULL,
            'startTime' => '2024-08-14 21:00:00',
            'endTime' => '2024-08-14 22:40:00',
            'status' => 'NS',
            'sport' => 'football',
            'active' => 1,
            'created_at' => '2024-08-05 17:21:34',
            'updated_at' => '2024-08-05 17:21:34',
            'deleted_at' => NULL,
        ]);
    }
}
