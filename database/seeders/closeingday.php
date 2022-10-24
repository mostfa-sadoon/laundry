<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class closeingday extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('closeingdaies')->insert([[
            'id' => 1,
        ],[
            'id' => 2,
        ],[
            'id' => 3,
        ],[
            'id'=>4
        ],[
            'id'=>5
        ],[
            'id'=>6
        ],[
            'id'=>7
        ]]);
    }

}
