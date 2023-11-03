<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRatingTelemedicineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rating_telemedicine', function (Blueprint $table) {
            $table->bigIncrements('id_rating_telemedicine');
            $table->integer('permintaan_telemedicine_id');
            $table->longText('comments')->nullable();
            $table->integer('star_rating')->nullable();
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
        Schema::dropIfExists('rating_telemedicine');
    }
}
