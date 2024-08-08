<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class LeaguesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('leagues')->delete();

        \DB::table('leagues')->insert([
            [
                'id' => 1,
                'leagueId' => NULL,
                'name' => 'English Premeire League',
                'sport' => 'football',
                'description' => 'It is the highest level of the English Football League system. 20 clubs are part of the league and they play a total of 38 matches in home and away match format',
                'image' => 'http://localhost/storage/uploads/9743979cf41241b0d30aba094853ce0b.png',
                'country' => 'UK',
                'season' => '2024',
                'active' => 1,
                'created_at' => '2024-08-05 17:09:36',
                'updated_at' => '2024-08-05 17:09:36',
                'deleted_at' => NULL,
            ], [
                'id' => 2,
                'leagueId' => NULL,
                'name' => 'Bundesliga',
                'sport' => 'football',
                'description' => 'At the top of the German football league system, the Bundesliga is Germany\'s primary football competition',
                'image' => 'http://localhost/storage/uploads/6037e4c526dfe0812c0c1691b5bdd954.png',
                'country' => 'GE',
                'season' => '2024',
                'active' => 1,
                'created_at' => '2024-08-05 17:13:20',
                'updated_at' => '2024-08-05 17:13:20',
                'deleted_at' => NULL,
            ]
        ]);
    }
}
