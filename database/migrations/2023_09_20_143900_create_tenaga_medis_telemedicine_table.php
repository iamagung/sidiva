<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenagaMedisTelemedicineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tenaga_medis_telemedicine', function (Blueprint $table) {
            $table->bigIncrements('id_tenaga_medis');
            $table->string('jenis_nakes');
            $table->string('poli_id',10);
            // $table->string('nama_nakes');
            $table->string('nakes_id');
            $table->string('no_telepon');
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
        Schema::dropIfExists('tenaga_medis_telemedicine');
    }
}
