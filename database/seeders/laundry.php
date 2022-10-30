<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class laundry extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('laundries')->insert([[
             'id'=>1,
             'email'=>'laundry@laundry.com',
             'phone'=>'0552342385',
             'country_code'=>'011',
             'status'=>'true',
             'branch'=>'one',
             'companyregister'=>'2023453',
             'taxcard'=>'2556230',
             'logo'=>'vfvdvfd.jpg',
             'password' => Hash::make(123456),
        ]]);
    }
}
