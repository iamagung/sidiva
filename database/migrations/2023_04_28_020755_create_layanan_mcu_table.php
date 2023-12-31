<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLayananMcuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('layanan_mcu', function (Blueprint $table) {
            $table->bigIncrements('id_layanan');
            $table->string('kategori_layanan');
            $table->string('nama_layanan');
            $table->text('deskripsi');
            $table->decimal('harga',12,2);
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
        Schema::dropIfExists('layanan_mcu');
    }
}
