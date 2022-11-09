<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class payment_method extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('payment_methods')->insert(
            [
               "id"=>1,
            ],[
              "id"=>2,
            ],[
                "id"=>3
            ]
      );
    }
}
