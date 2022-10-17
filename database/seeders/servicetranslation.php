<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class servicetranslation extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('servicetranslations')->insert([[
            'id' => 1,
            'service_id'=>1,
            'name'=>'washing',
            'locale'=>'en',
        ],[
            'id' => 2,
            'service_id'=>1,
            'name'=>'غسيل',
            'locale'=>'ar',
        ],[
            'id' => 3,
            'service_id'=>2,
            'name'=>'dry clean',
            'locale'=>'en',
        ],[
            'id' => 4,
            'service_id'=>2,
            'name'=>'تجفيف',
            'locale'=>'ar',
        ],[
            'id' => 5,
            'service_id'=>3,
            'name'=>'ironing',
            'locale'=>'en',
        ],[
            'id' => 6,
            'service_id'=>3,
            'name'=>'مكوي',
            'locale'=>'ar',
        ],[
            'id' => 7,
            'service_id'=>4,
            'name'=>'washing&ironing',
            'locale'=>'en',
        ],[
            'id' => 8,
            'service_id'=>4,
            'name'=>'غسيل ومكوي',
            'locale'=>'ar',
        ]]);
    }
}
