<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResepObatDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resep_obat_detail', function (Blueprint $table) {
            $table->bigIncrements('id_resep_obat_detail');
            $table->integer('resep_obat_id');
            $table->integer('id_obat');
            $table->integer('qty');
            $table->decimal('harga', 12, 2);
            $table->string('nama_obat', 255);
            $table->string('kode_obat', 25);
            $table->string('signa', 25);
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
        Schema::dropIfExists('resep_obat_detail');
    }
}
