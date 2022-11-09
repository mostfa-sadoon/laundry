<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class delivery_type_trnaslations extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('delivery_type_translations')->insert(
            [
               "id"=>1,
               "delivery_type_id"=>1,
               "locale"=>"ar",
               "name"=>"بواسطه الديليفري",
            ],[
                "id"=>2,
                "delivery_type_id"=>1,
                "locale"=>"en",
                "name"=>"by delivery",
            ],[
                "id"=>3,
                "delivery_type_id"=>1,
                "locale"=>"ar",
                "name"=>"يستلمه بنفسه",
            ],
            [
                "id"=>4,
               "delivery_type_id"=>1,
               "locale"=>"en",
               "name"=>"self delivery",
             ]
      );
    }
}
