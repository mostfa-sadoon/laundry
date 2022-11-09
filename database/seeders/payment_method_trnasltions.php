<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class payment_method_trnasltions extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('payment_method_translations')->insert(
            [
               "id"=>1,
               "payment_method_id"=>1,
               "locale"=>"ar",
               "name"=>"دفع نقدي",
            ],[
                "id"=>2,
                "payment_method_id"=>1,
                "locale"=>"en",
                "name"=>"cash",
            ],[
                "id"=>3,
                "payment_method_id"=>1,
                "locale"=>"ar",
                "name"=>"فيزا",
            ],
            [
                "id"=>4,
               "payment_method_id"=>1,
               "locale"=>"en",
               "name"=>"credit",
             ],[
                "id"=>5,
                "payment_method_id"=>1,
                "locale"=>"ar",
                "name"=>"محفظه اليكترونيه",
            ],
            [
                "id"=>6,
               "payment_method_id"=>1,
               "locale"=>"en",
               "name"=>"wallet",
             ]
      );
    }
}
