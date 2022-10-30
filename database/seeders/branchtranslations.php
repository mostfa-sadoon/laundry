<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class branchtranslations extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('branchtranslations')->insert([[
            'id' => 1,
            'branch_id'=>1,
            'name'=>'nazifaa',
            'locale'=>'en',
        ],
        [
            'id' => 2,
            'branch_id'=>1,
            'name'=>'نظيفه',
            'locale'=>'ar',
        ],[
            'id' => 3,
            'branch_id'=>2,
            'name'=>'nazifaa seconde ',
            'locale'=>'en',
        ],
        [
            'id' => 4,
            'branch_id'=>2,
            'name'=>'نظيفه الفرع الثاني',
            'locale'=>'ar',
        ]]);
    }
}
