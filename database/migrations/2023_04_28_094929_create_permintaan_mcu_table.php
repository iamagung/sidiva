<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermintaanMcuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permintaan_mcu', function (Blueprint $table) {
            $table->bigIncrements('id_permintaan');
            $table->string('no_rm')->nullable();
            $table->string('no_registrasi');
            $table->string('kode_booking');
            $table->string('no_antrian')->nullable();
            $table->string('nik');
            $table->string('nama')->nullable();
            $table->string('alamat')->nullable();
            $table->string('layanan_id');
            $table->date('tanggal_order');
            $table->date('tanggal_kunjungan');
            $table->string('jenis_pembayaran');
            $table->string('no_bpjs')->nullable();
            $table->string('no_rujukan')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('telepon')->nullable();
            $table->integer('biaya')->nullable();
            $table->string('status_pembayaran')->nullable();
            $table->string('metode_pembayaran')->nullable();
            $table->string('status_pasien')->nullable()->comment('belum, proses, selesai, batal');
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
        Schema::dropIfExists('permintaan_mcu');
    }
}
