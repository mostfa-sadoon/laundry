<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OrderDetailes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('order_detailes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->unsignedBigInteger('branchitem_id');
            $table->foreign('branchitem_id')->references('id')->on('branchitems')->onDelete('cascade');
            $table->unsignedBigInteger('service_id')->nullable();
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            $table->unsignedBigInteger('additionalservice_id')->nullable();
            $table->foreign('additionalservice_id')->references('id')->on('additionalservices')->onDelete('cascade');
            $table->double('price');
            $table->integer('quantity');
            // $table->unsignedBigInteger('items_price_id');
            // $table->foreign('items_price_id')->references('id')->on('serviceitems_price')->onDelete('cascade');
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
