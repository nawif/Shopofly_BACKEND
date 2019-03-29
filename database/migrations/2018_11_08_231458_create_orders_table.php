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
            $table->timestamps();

            $table->string('status')->default('Order Placed');

            $table->unsignedInteger('user_id');
            $table->foreign('user_id')
            ->references('id')->on('users')
            ->onDelete('cascade');

            $table->integer('address_id')->unsigned()->nullable();
            $table->foreign('address_id')
            ->references('id')->on('addresses')
            ->onDelete('cascade');

            $table->integer('delivery_agent_id')->unsigned()->nullable();
            $table->foreign('delivery_agent_id')
            ->references('id')->on('users')
            ->onDelete('cascade');

            $table->integer('transaction_id')->unsigned()->nullable();
            $table->foreign('transaction_id')
            ->references('id')->on('transactions')
            ->onDelete('cascade');
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
