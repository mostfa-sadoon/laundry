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
        DB::table('itemtranslations')->insert([
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
            'id' => 1,
            'item_id'=>1,
            'name'=>'trouser',
            'locale'=>'en',
        ],[
            'id' => 2,
            'item_id'=>1,
            'name'=>'بنطال',
            'locale'=>'ar',
        ],[
            'id' => 1,
            'item_id'=>1,
            'name'=>'dress',
            'locale'=>'en',
        ],[
            'id' => 2,
            'item_id'=>1,
            'name'=>'فستان',
            'locale'=>'ar',
        ]);
    }
}
