<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Deliver extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->uniqe();
            $table->string('country_code');
            $table->string('phone')->uniqe();
            $table->string('email')->uniqe();
            $table->enum('status',['online','ofline']);
            $table->string('otp')->nullable();
            $table->string('password')->nullable();
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
