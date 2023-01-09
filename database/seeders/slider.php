<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class slider extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
        {
            //
            DB::table('sliders')->insert([[
                'id' => 1,
            ],[
                'id' => 2,
            ],[
                'id' => 3,
            ]]);
        }
}
