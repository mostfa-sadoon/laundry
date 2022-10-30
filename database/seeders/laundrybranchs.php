<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class laundrybranchs extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('laundry_branches')->insert([[
            'id'=>1,
            'laundry_id'=>1,
            'branch_id'=>1,
        ],[
            'id'=>2,
            'laundry_id'=>1,
            'branch_id'=>2,
        ]]);
    }
}
