<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLayananHcTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('layanan_hc', function (Blueprint $table) {
            $table->bigIncrements('id_layanan_hc');
            $table->string('jenis_layanan');
            $table->string('nama_layanan');
            $table->decimal('harga',12,2);
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
        Schema::dropIfExists('layanan_hc');
    }
}
