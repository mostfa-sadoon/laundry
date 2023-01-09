<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class slidertranslation extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('slider_translations')->insert([[
            'id' => 1,
            'slider_id'=>1,
            'img'=>'uploads/slider/en/test1.jpg',
            'locale'=>'en',
        ],[
            'id' => 2,
            'slider_id'=>1,
            'img'=>'uploads/slider/ar/test1.jpg',
            'locale'=>'ar',
        ],[
            'id' => 3,
            'slider_id'=>2,
            'img'=>'uploads/slider/en/test2.jpg',
            'locale'=>'en',
        ],[
            'id' => 4,
            'slider_id'=>2,
            'img'=>'uploads/slider/ar/test2.jpg',
            'locale'=>'ar',
        ],[
            'id' => 5,
            'slider_id'=>3,
            'img'=>'uploads/slider/en/test3.jpg',
            'locale'=>'en',
        ],[
            'id' => 6,
            'slider_id'=>3,
            'img'=>'uploads/slider/ar/test3.jpg',
            'locale'=>'ar',
        ]]);
    }
}
