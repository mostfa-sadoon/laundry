<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Branchnotify extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('branchnotifytype', function (Blueprint $table) {
            $table->id();
            $table->boolean('status')->default(true);
            $table->unsignedBigInteger('branch_id');
            $table->foreign('branch_id')->references('id')->on('branchs')->onDelete('cascade');
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
