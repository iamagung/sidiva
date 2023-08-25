<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermintaanHcTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permintaan_hc', function (Blueprint $table) {
            $table->bigIncrements('id_permintaan_hc');
            $table->integer('layanan_hc_id');
            $table->integer('paket_hc_id');
            $table->string('no_rm')->nullable();
            $table->string('no_registrasi')->nullable();
            $table->string('nik');
            $table->string('nama')->nullable();
            $table->string('no_bpjs')->nullable();
            $table->string('no_rujukan')->nullable();
            $table->date('tanggal_order');
            $table->date('tanggal_kunjungan');
            $table->date('tanggal_lahir')->nullable();
            $table->string('alamat')->nullable();
            $table->string('alergi_pasien')->nullable();
            $table->string('latitude');
            $table->string('longitude');
            $table->string('jenis_kelamin');
            $table->string('no_telepon');
            $table->string('jenis_pembayaran');
            $table->integer('biaya_layanan')->nullable();
            $table->integer('biaya_ke_lokasi')->nullable();
            $table->string('status_pasien')->nullable()->comment('belum, proses, batal, selesai');
            $table->string('status_pembayaran')->nullable();
            $table->string('metode_pembayaran')->nullable();
            $table->integer('tenaga_medis_id')->nullable();
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
        Schema::dropIfExists('permintaan_hc');
    }
}
