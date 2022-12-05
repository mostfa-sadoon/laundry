<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class notificationtype extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('notificationtypes')->insert([[
            'id' => 1,
        ],[
            'id' => 2,
        ],[
            'id' => 3,
        ],[
            'id' => 4,
        ]]);

    }
}
