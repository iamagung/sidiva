<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermintaanTelemedicineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permintaan_telemedicine', function (Blueprint $table) {
            $table->bigIncrements('id_permintaan_telemedicine');
            $table->string('nik');
            $table->string('no_rm')->nullable();
            $table->string('no_registrasi')->nullable();
            $table->string('no_resep')->nullable();
            $table->string('nama');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('jenis_kelamin');
            $table->string('no_telepon');
            $table->string('alamat');
            $table->string('latitude');
            $table->string('longitude');
            $table->text('keterangan');
            $table->date('tanggal_order');
            $table->date('tanggal_kunjungan');
            $table->string('poli_id');
            $table->string('keluhan');
            $table->integer('dokter_id');
            $table->string('jadwal_dokter');
            $table->integer('biaya_layanan');
            $table->integer('biaya_ke_lokasi');
            $table->string('jenis_pembayaran')->comment('Tunai/Non Tunai');
            $table->string('status_pembayaran')->comment('Belum/Lunas');
            $table->string('status_pasien')->comment('belum, proses, batal, selesai');
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
        Schema::dropIfExists('permintaan_telemedicine');
    }
}
