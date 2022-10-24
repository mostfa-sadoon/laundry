<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(service::class);
        $this->call(servicetranslation::class);
        $this->call(category::class);
        $this->call(categorytranslations::class);
        $this->call(categoryservices::class);
        $this->call(laundry::class);
        $this->call(branch::class);
        $this->call(branchtranslations::class);
        $this->call(laundrybranchs::class);
        $this->call(items::class);
        $this->call(itemtranslations::class);
        $this->call(closeingday::class);
        $this->call(closeingdaytranslation::class);
        // \App\Models\User::factory(10)->create();
    }
}
