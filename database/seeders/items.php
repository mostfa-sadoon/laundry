<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Seeder;

class items extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('items')->insert([[
            'id' => 1,
            'category_id'=>1
        ],[
            'id' => 2,
            'category_id'=>1
        ],[
            'id' => 3,
            'category_id'=>1
        ]],
    );
    }
}
