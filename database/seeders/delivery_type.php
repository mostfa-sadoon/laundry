<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class delivery_type extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('delivery_types')->insert([
              [
                 "id"=>1,
              ],[
                "id"=>2,
              ],[
                "id"=>3,
              ]
        ]);

    }
}
