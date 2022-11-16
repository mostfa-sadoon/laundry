<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class driver extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('drivers')->insert([
            [
               "id"=>1,
               "name"=>'mostafa saadoun',
               "email"=>'sadoon @gmail',
               "phone"=>'01014324321',
               "country_code"=>'011',
               "status"=>'online'
            ], [
                "id"=>2,
                "name"=>'nada',
                "email"=>'nada@gmail.com',
                "phone"=>'01154894535',
                "country_code"=>'011',
                "status"=>'online'
             ], [
                "id"=>3,
                "name"=>'mubarak',
                "email"=>'mubark@gmail.com',
                "phone"=>'01066074066',
                "country_code"=>'011',
                "status"=>'online'
             ], [
                "id"=>4,
                "name"=>'Ahmed saadoun',
                "email"=>'ahmed@gmail.com',
                "phone"=>'05525222233',
                "country_code"=>'011',
                "status"=>'offline'
             ], [
                "id"=>5,
                "name"=>'Hamdey',
                "email"=>'Hamdey@gmail.com',
                "phone"=>'05588996652',
                "country_code"=>'011',
                "status"=>'online'
             ],
      ]);

    }
}
