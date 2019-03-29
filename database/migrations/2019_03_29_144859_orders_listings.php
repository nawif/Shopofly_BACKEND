<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OrdersListings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders_listings', function (Blueprint $table) {
            $table->unsignedInteger('order_id');
            $table->foreign('order_id')
                ->references('id')->on('orders');

            $table->unsignedInteger('listing_id');
            $table->foreign('listing_id')
                ->references('id')->on('listings');
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
