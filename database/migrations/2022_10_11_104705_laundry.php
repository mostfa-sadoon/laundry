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
            $table->string('name')->unique();
            $table->enum('status',['true','false'])->default('true');
            $table->enum('branch',['one','many']);
            $table->string('companyregister');
            $table->string('taxcard');
            $table->string('fingre_print')->unique()->nullable();
            $table->string('face_id')->unique()->nullable();
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
