<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRatingMcuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rating_mcu', function (Blueprint $table) {
            $table->bigIncrements('id_rating_mcu');
            $table->integer('permintaan_mcu_id');
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
        Schema::dropIfExists('rating_mcu');
    }
}
