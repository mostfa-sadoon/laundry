<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class Aditionalservice extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('additionalservices')->insert([[
            'id' => 1,
        ],[
            'id' => 2,
        ]]);
    }
}
