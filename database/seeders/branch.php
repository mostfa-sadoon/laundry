<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class branch extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('branchs')->insert([
            'id'=>1,
            'email'=>'branch@laundry.com',
            'username'=>'branchusername',
            'country_code'=>'011',
            'phone'=>'2342385',
            'status'=>'open',
            'lat'=>'2023453',
            'long'=>'2556230',
            'laundry_id'=>1,
            'password' => Hash::make(123456),
        ]);
    }
}
