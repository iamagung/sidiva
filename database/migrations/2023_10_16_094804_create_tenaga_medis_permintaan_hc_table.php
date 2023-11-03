<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenagaMedisPermintaanHcTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tenaga_medis_permintaan_hc', function (Blueprint $table) {
            $table->bigIncrements('id_tenaga_medis_permintaan_hc');
            $table->integer('tenaga_medis_id');
            $table->integer('permintaan_hc_id');
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
        Schema::dropIfExists('tenaga_medis_permintaan_hc');
    }
}
