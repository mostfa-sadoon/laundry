<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AditionalserviceTranslations extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('additionalservicetranslations')->insert([[
            'id' => 1,
            'additionalservice_id'=>1,
            'name'=>'Perfuming',
            'locale'=>'en',
        ],[
            'id' => 2,
            'additionalservice_id'=>1,
            'name'=>'تعطير',
            'locale'=>'ar',
        ],[
        'id' => 3,
        'additionalservice_id'=>2,
        'name'=>'Folding',
        'locale'=>'en',
        ],[
            'id' => 4,
            'additionalservice_id'=>2,
            'name'=>'تطبيق',
            'locale'=>'ar',
        ]]);
    }
}
