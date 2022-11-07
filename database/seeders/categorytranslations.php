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
        ],[
        'id' => 3,
        'category_id'=>2,
        'name'=>'carpets',
        'locale'=>'en',
        ],[
            'id' => 4,
            'category_id'=>2,
            'name'=>'سجاد',
            'locale'=>'ar',
        ],[
        'id' => 5,
        'category_id'=>3,
        'name'=>'Furniture',
        'locale'=>'en',
        ],[
            'id' => 6,
            'category_id'=>3,
            'name'=>'مفروشات',
            'locale'=>'ar',
        ],[
            'id' => 7,
            'category_id'=>4,
            'name'=>'cars',
            'locale'=>'en',
            ],[
                'id' => 8,
                'category_id'=>4,
                'name'=>'سيارات',
                'locale'=>'ar',
            ]]);
    }
}
