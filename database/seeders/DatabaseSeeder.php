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
        $this->call(driver::class);
        $this->call(service::class);
        $this->call(servicetranslation::class);
        $this->call(Aditionalservice::class);
        $this->call(AditionalserviceTranslations::class);
        $this->call(category::class);
        $this->call(categorytranslations::class);
        $this->call(categoryservices::class);
        $this->call(additionalservice_category::class);
        $this->call(laundry::class);
        $this->call(branch::class);
        $this->call(branchtranslations::class);
        $this->call(laundrybranchs::class);
        $this->call(items::class);
        $this->call(itemtranslations::class);
        $this->call(closeingday::class);
        $this->call(closeingdaytranslation::class);
        $this->call(payment_method::class);
        $this->call(payment_method_trnasltions::class);
        $this->call(delivery_type::class);
        $this->call(delivery_type_trnaslations::class);
        $this->call(notificationtype::class);
        $this->call(notificationtypetranslations::class);
        $this->call(slider::class);
        $this->call(slidertranslation::class);
        // \App\Models\User::factory(10)->create();
    }
}
