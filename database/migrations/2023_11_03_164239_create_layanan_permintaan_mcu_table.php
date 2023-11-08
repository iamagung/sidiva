<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLayananPermintaanMcuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('layanan_permintaan_mcu', function (Blueprint $table) {
            $table->bigIncrements('id_layanan_permintaan_mcu');
            $table->integer('permintaan_id');
            $table->integer('layanan_id');
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
        Schema::dropIfExists('layanan_permintaan_mcu');
    }
}
