<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class categorytranslations extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('categorytranslations')->insert([[
            'id' => 1,
            'category_id'=>1,
            'name'=>'clothes',
            'locale'=>'en',
        ],[
            'id' => 2,
            'category_id'=>1,
            'name'=>'ملابس',
            'locale'=>'ar',
        ]]);
    }
}
