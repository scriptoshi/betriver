<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TeamsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('teams')->delete();
        \DB::table('teams')->insert([
            [
                'id' => 1,
                'teamId' => NULL,
                'name' => 'Manchester United',
                'code' => 'MANU',
                'sport' => 'football',
                'country' => 'UK',
                'description' => 'A professional football club based in Old Trafford, Greater Manchester, England',
                'image' => 'http://localhost/storage/uploads/d6729f652c31ceabb8a490a6ae9d32cc.jpg',
                'active' => 1,
                'created_at' => '2024-08-05 17:16:37',
                'updated_at' => '2024-08-05 17:16:37',
                'deleted_at' => NULL,
            ],
            [
                'id' => 2,
                'teamId' => NULL,
                'name' => 'Arsenl FC',
                'code' => 'AFC',
                'sport' => 'football',
                'country' => 'UK',
                'description' => 'A professional football club based in Holloway, North London, England.',
                'image' => 'http://localhost/storage/uploads/6e5a7a685be6e2a02a2d5e7600837a17.png',
                'active' => 1,
                'created_at' => '2024-08-05 17:21:08',
                'updated_at' => '2024-08-05 17:21:08',
                'deleted_at' => NULL,
            ]
        ]);
    }
}
