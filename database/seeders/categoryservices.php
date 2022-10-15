<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class categoryservices extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('categoryservices')->insert([
            'id' => 1,
            'category_id'=>1,
            'service_id'=>1,

        ],[
            'id' => 2,
            'category_id'=>1,
            'service_id'=>2,
        ]
        ,[
            'id' => 2,
            'category_id'=>1,
            'service_id'=>3,
        ],[
            'id' => 2,
            'category_id'=>1,
            'service_id'=>4,
        ]);
    }
}
