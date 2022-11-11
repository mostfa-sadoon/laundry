<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Order extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->enum("status",['payied','unpaied'])->default('unpaied');
            $table->boolean('checked')->default(false);
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_location');
            $table->string('lat');
            $table->string('long');
            $table->unsignedBigInteger('branch_id');
            $table->foreign('branch_id')->references('id')->on('branchs')->onDelete('cascade');
            $table->unsignedBigInteger('delivery_type_id')->nullable();
            $table->foreign('delivery_type_id')->references('id')->on('delivery_types')->onDelete('cascade');
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->onDelete('cascade');
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
