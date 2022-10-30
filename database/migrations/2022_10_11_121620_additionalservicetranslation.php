<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Additionalservicetranslation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('additionalservicetranslations', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->string('locale')->index();
            $table->unique(['additionalservice_id', 'locale']);
            $table->unsignedBigInteger('additionalservice_id');
            $table->foreign('additionalservice_id')->references('id')->on('additionalservices')->onDelete('cascade');
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
