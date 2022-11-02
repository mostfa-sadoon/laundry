<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class additionalservice_category extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('additionalservice_category')->insert([[
            'id' => 1,
            'category_id'=>1,
            'additionalservice_id'=>1,

        ],  ['id' => 2,
            'category_id'=>2,
            'additionalservice_id'=>1,

         ],[
            'id' => 3,
            'category_id'=>1,
            'additionalservice_id'=>2,
        ],[
            'id' => 4,
            'category_id'=>2,
            'additionalservice_id'=>2,
        ]
    ]);
    }
}
