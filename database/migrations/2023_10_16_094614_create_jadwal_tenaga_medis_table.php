<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJadwalTenagaMedisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jadwal_tenaga_medis', function (Blueprint $table) {
            $table->bigIncrements('id_jadwal_tenaga_medis');
            $table->string('nakes_id', 10);
            $table->time('jam_awal');
            $table->time('jam_akhir');
            $table->string('jenis_pelayanan', 25);
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
        Schema::dropIfExists('jadwal_tenaga_medis');
    }
}
