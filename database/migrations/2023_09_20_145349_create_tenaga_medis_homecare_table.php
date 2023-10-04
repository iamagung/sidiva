<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenagaMedisHomecareTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tenaga_medis_homecare', function (Blueprint $table) {
            $table->bigIncrements('id_tenaga_medis');
            $table->string('jenis_nakes');
            $table->string('nama_nakes');
            $table->string('layanan_id');
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
        Schema::dropIfExists('tenaga_medis_homecare');
    }
}
