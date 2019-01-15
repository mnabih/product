<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('totalPrice')->unsigned();
            $table->integer('finish')->default(0);
            $table->integer('status')->default(0);
            $table->integer('owner_delete')->default(0);
            $table->date('finish_date')->nullable();
            $table->integer('delivery_id')->unsigned();
            $table->foreign('delivery_id')->references('id')->on('users');
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            //$table->softDeletes();
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
