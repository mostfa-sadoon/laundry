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
        DB::table('delivery_type_translations')->insert([
            [
               "id"=>1,
               "delivery_type_id"=>1,
               "name"=>"بواسطه الديليفري",
               "locale"=>"ar",
            ],[
                "id"=>2,
                "delivery_type_id"=>1,
                "name"=>"by delivery",
                "locale"=>"en",
            ],[
                "id"=>3,
                "delivery_type_id"=>2,
                "name"=>"يستلمه بنفسه",
                "locale"=>"ar",
            ],
            [
                "id"=>4,
               "delivery_type_id"=>2,
               "name"=>"self delivery",
               "locale"=>"en",
             ]
      ]);
    }
}
