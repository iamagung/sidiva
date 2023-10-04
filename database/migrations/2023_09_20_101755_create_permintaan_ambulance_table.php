<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermintaanAmbulanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permintaan_ambulance', function (Blueprint $table) {
            $table->bigIncrements('id_permintaan_ambulance');
            $table->string('nik');
            $table->string('nama');
            $table->date('tanggal_kunjungan');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('jenis_kelamin');
            $table->string('alamat');
            $table->string('no_telepon');
            $table->string('latitude_jemput')->nullable();
            $table->string('longitude_jemput')->nullable();
            $table->string('latitude_antar')->nullable();
            $table->string('longitude_antar')->nullable();
            $table->integer('jenis_layanan');
            $table->string('keterangan')->nullable();
            $table->string('jenis_pembayaran');
            $table->string('status_pembayaran');
            $table->integer('biaya_ke_lokasi')->nullable();
            $table->string('status_pasien')->nullable()->comment('belum, proses, batal, selesai');
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
        Schema::dropIfExists('permintaan_ambulance');
    }
}
