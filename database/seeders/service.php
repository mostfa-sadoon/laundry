<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class service extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('services')->insert([[
            'id' => 1,
        ],[
            'id' => 2,
        ],[
            'id' => 3,
        ],[
            'id'=>4
        ]]);
    }
}
