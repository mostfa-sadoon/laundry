<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class branchitemtranslations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('branchitemtranslations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('locale')->index();
            $table->unique(['branchitem_id', 'locale']);
            $table->unsignedBigInteger('branchitem_id');
            $table->foreign('branchitem_id')->references('id')->on('branchitems')->onDelete('cascade');
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
