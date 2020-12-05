<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_lists', function (Blueprint $table) {
            $table->id();
            $table->string('ProductName');
            $table->unsignedBigInteger('Media_Id');
            $table->integer('TotalPrice');
            $table->decimal('Price');
            $table->longText('Description');
            $table->integer('SKU');
            $table->integer('Quantity');
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
        Schema::dropIfExists('cart_lists');
    }
}
