<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class branch extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('branchs', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('username')->unique();
            $table->string('country_code');
            $table->string('phone')->unique();
            $table->string('lat');
            $table->string('long');
            $table->string('password');
            $table->enum('status',['open','closed']);
            $table->timestamp('open_time')->nullable();
            $table->timestamp('closed_time')->nullable();
            $table->unsignedBigInteger('laundry_id');
            $table->foreign('laundry_id')->references('id')->on('laundries')->onDelete('cascade');
            $table->string('logo');
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
