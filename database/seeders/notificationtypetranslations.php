<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class notificationtypetranslations extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('notificationtypetranslations')->insert([[
            'id' => 1,
            'notificationtype_id'=>1,
            'name'=>'New offers',
            'locale'=>'en',
        ],
        [
            'id' => 2,
            'notificationtype_id'=>1,
            'name'=>'طلبات جديده',
            'locale'=>'ar',
        ],[
            'id' => 3,
            'notificationtype_id'=>2,
            'name'=>'Rate Orders',
            'locale'=>'en',
        ],
        [
            'id' => 4,
            'notificationtype_id'=>2,
            'name'=>'تقييم الطلبات',
            'locale'=>'ar',
        ],[
            'id' => 5,
            'notificationtype_id'=>3,
            'name'=>'order status',
            'locale'=>'en',
        ],
        [
            'id' => 6,
            'notificationtype_id'=>3,
            'name'=>'حاله الطلب',
            'locale'=>'ar',
        ],[
            'id' => 7,
            'notificationtype_id'=>4,
            'name'=>'order ready',
            'locale'=>'en',
        ],
        [
            'id' => 8,
            'notificationtype_id'=>4,
            'name'=>'جاهزيه الطلب',
            'locale'=>'ar',
        ]]);
    }
}
