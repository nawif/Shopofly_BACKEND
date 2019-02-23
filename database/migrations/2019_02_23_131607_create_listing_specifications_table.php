<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateListingSpecificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('listing_specifications', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('type');
            $table->string('key');
            $table->string('value');

            $table->unsignedInteger('listing_id');
            $table->foreign('listing_id')
            ->references('id')->on('listings')
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
        Schema::dropIfExists('listing_specifications');
    }
}
