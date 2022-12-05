<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Notificationtypetranslations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('notificationtypetranslations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('locale')->index();
            $table->unique(['notificationtype_id', 'locale']);
            $table->unsignedBigInteger('notificationtype_id');
            $table->foreign('notificationtype_id')->references('id')->on('notificationtypes')->onDelete('cascade');
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
