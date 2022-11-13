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
            $table->string('username')->unique();
            $table->string('country_code');
            $table->string('phone')->unique();
            $table->boolean('argent')->default(true);
            $table->string('lat');
            $table->string('long');
            $table->string('password');
            $table->enum('status',['open','closed']);
            $table->time('open_time')->nullable();
            $table->time('closed_time')->nullable();
            $table->unsignedBigInteger('laundry_id');
            $table->string('address');
            $table->foreign('laundry_id')->references('id')->on('laundries')->onDelete('cascade');
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
