<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class closeingdaytranslation extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('closeingdaytranslations')->insert([[
            'id' => 1,
            'closeingday_id' => 1,
             'name'=>'saturday',
             'locale'=>'en'
        ],[
            'id' => 2,
            'closeingday_id' => 1,
             'name'=>'السبت',
             'locale'=>'ar'
        ],[
            'id' => 3,
            'closeingday_id' => 2,
             'name'=>'Sunday',
             'locale'=>'en'
        ],[
            'id' => 4,
            'closeingday_id' => 2,
             'name'=>'الاحد',
             'locale'=>'ar'
        ],[
            'id' => 5,
            'closeingday_id' => 3,
             'name'=>'Monday',
             'locale'=>'en'
        ],[
            'id' => 6,
            'closeingday_id' => 3,
             'name'=>'الاتنين',
             'locale'=>'ar'
        ],[
            'id' => 7,
            'closeingday_id' => 4,
             'name'=>'Tuesday',
             'locale'=>'en'
        ],[
            'id' => 8,
            'closeingday_id' => 4,
             'name'=>'الثلاثاء',
             'locale'=>'ar'
        ],[
            'id' => 9,
            'closeingday_id' => 5,
             'name'=>'Wednesday',
             'locale'=>'en'
        ],[
            'id' => 10,
            'closeingday_id' => 5,
             'name'=>'الاربعاء',
             'locale'=>'ar'
        ],[
            'id' => 11,
            'closeingday_id' => 6,
             'name'=>'Thursday',
             'locale'=>'en'
        ],[
            'id' => 12,
            'closeingday_id' => 6,
             'name'=>'الخميس',
             'locale'=>'ar'
        ],[
            'id' => 13,
            'closeingday_id' => 7,
             'name'=>'Friday',
             'locale'=>'en'
        ],[
            'id' => 14,
            'closeingday_id' => 7,
             'name'=>'الجمعه',
             'locale'=>'ar'
        ],]);
    }
}
