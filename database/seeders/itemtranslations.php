<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class itemtranslations extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('itemtranslations')->insert([[
            'id' => 1,
            'item_id'=>1,
            'name'=>'shirt',
            'locale'=>'en',
        ],[
            'id' => 2,
            'item_id'=>1,
            'name'=>'تيشيرت',
            'locale'=>'ar',
        ],[
            'id' => 3,
            'item_id'=>2,
            'name'=>'trouser',
            'locale'=>'en',
        ],[
            'id' => 4,
            'item_id'=>2,
            'name'=>'بنطال',
            'locale'=>'ar',
        ],[
            'id' => 5,
            'item_id'=>3,
            'name'=>'dress',
            'locale'=>'en',
        ],[
            'id' => 6,
            'item_id'=>3,
            'name'=>'فستان',
            'locale'=>'ar',
        ],[
            'id' => 7,
            'item_id'=>4,
            'name'=>'carpets',
            'locale'=>'en',
        ],[
            'id' => 8,
            'item_id'=>4,
            'name'=>'سجاد فاخر',
            'locale'=>'ar',
        ],[
            'id' => 9,
            'item_id'=>5,
            'name'=>'home furniture',
            'locale'=>'en',
        ],[
            'id' => 10,
            'item_id'=>5,
            'name'=>'مفروشات منزليه',
            'locale'=>'ar',
        ],[
            'id' => 11,
            'item_id'=>6,
            'name'=>'car 1',
            'locale'=>'en',
        ],[
            'id' => 12,
            'item_id'=>6,
            'name'=>'عربيه واحد ',
            'locale'=>'ar',
        ]

        ]);
    }
}
