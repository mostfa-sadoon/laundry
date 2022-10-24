<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Laundry extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('laundries', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('country_code');
            $table->string('phone')->unique();
            $table->string('password');
            $table->enum('status',['active','disactive']);
            $table->enum('branch',['one','multiple']);
            $table->string('companyregister');
            $table->string('taxcard');
            $table->string('logo')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
