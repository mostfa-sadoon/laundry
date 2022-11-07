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
        DB::table('category_service')->insert([[
            'id' => 1,
            'category_id'=>1,
            'service_id'=>1,

        ],  ['id' => 2,
            'category_id'=>2,
            'service_id'=>1,

         ],[
            'id' => 3,
            'category_id'=>1,
            'service_id'=>2,
        ],[
            'id' => 4,
            'category_id'=>2,
            'service_id'=>2,
        ],[
            'id' => 5,
            'category_id'=>1,
            'service_id'=>3,
        ],[
            'id' => 6,
            'category_id'=>1,
            'service_id'=>4,
        ]

        ,[
            'id' => 7,
            'category_id'=>3,
            'service_id'=>1,
        ],[
            'id' => 8,
            'category_id'=>4,
            'service_id'=>1,
        ]
    ]);
    }
}
