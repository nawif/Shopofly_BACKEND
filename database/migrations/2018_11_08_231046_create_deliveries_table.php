<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('delivery_agent_id')->unsigned();;

            $table->integer('transaction_id')->unsigned();;

            $table->string('status')->default('record created');

            $table->timestamps();
        });

        Schema::table('deliveries', function (Blueprint $table) {
            $table->foreign('delivery_agent_id')
            ->references('id')->on('users');

            $table->foreign('transaction_id')
            ->references('id')->on('transactions');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deliveries');
    }
}
