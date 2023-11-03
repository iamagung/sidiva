<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRekamMedisLanjutanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rekam_medis_lanjutan', function (Blueprint $table) {
            $table->bigIncrements('id_rekam_medis_lanjutan');
            $table->integer('permintaan_id');
            $table->string('jenis_layanan',25);
            $table->integer('perawat_id');
            $table->text('anamnesis')->nullable();
            $table->text('pemeriksaan_fisik')->nullable();
            $table->text('assessment')->nullable();
            $table->text('rencana_dan_terapi')->nullable();
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
        Schema::dropIfExists('rekam_medis_lanjutan');
    }
}
