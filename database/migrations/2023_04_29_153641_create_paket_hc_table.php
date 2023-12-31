<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaketHcTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paket_hc', function (Blueprint $table) {
            $table->bigIncrements('id_paket_hc');
            $table->string('jenis_layanan');
            $table->string('nama_paket');
            $table->integer('harga');
            $table->string('jumlah_hari');
            $table->text('deskripsi');
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
        Schema::dropIfExists('paket_hc');
    }
}
