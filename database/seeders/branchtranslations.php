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
        DB::table('branchtranslations')->insert([
            'id' => 1,
            'branch_id'=>1,
            'name'=>'nazifaa',
            'locale'=>'en',
        ],
        [
            'id' => 1,
            'branch_id'=>1,
            'name'=>'نظيفه',
            'locale'=>'ar',
        ]);
    }
}
