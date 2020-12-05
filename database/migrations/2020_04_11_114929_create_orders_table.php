<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_no');
            $table->string('firstname');
            $table->string('lastname');
            $table->integer('mobile_no');
            $table->integer('other_mobile_no');
            $table->text('delivery_address');
            $table->integer('state_region_id');
            $table->unsignedBigInteger('billing_id');
            $table->unsignedBigInteger('shipping_id');
            $table->string('customer_id');
            $table->string('status');
            $table->integer('total');
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
        Schema::dropIfExists('orders');
    }
}
