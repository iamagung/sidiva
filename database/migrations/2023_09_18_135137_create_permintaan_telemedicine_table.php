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
            $table->string('jenis_kelamin',50);
            $table->string('alamat');
            $table->string('no_telepon',50);
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->text('keterangan');
            $table->datetime('tanggal_order');
            $table->date('tanggal_kunjungan');
            $table->string('poli_id',25);
            $table->string('keluhan');
            $table->integer('tenaga_medis_id');
            $table->integer('perawat_id')->nullable();
            $table->string('jadwal_dokter',50);
            $table->decimal('biaya_layanan', 12, 2);
            $table->decimal('jarak', 4, 1);
            $table->string('jenis_pembayaran')->comment('Tunai/Non Tunai')->nullable();
            $table->string('metode_pembayaran')->comment('Vendor payment yang digunakan')->nullable();
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
