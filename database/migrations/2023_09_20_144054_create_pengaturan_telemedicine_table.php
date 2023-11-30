<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePengaturanTelemedicineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pengaturan_telemedicine', function (Blueprint $table) {
            $table->bigIncrements('id_pengaturan_telemedicine');
            $table->string('seninBuka')->nullable();
            $table->string('seninTutup')->nullable();
            $table->string('selasaBuka')->nullable();
            $table->string('selasaTutup')->nullable();
            $table->string('rabuBuka')->nullable();
            $table->string('rabuTutup')->nullable();
            $table->string('kamisBuka')->nullable();
            $table->string('kamisTutup')->nullable();
            $table->string('jumatBuka')->nullable();
            $table->string('jumatTutup')->nullable();
            $table->string('sabtuBuka')->nullable();
            $table->string('sabtuTutup')->nullable();
            $table->string('mingguBuka')->nullable();
            $table->string('mingguTutup')->nullable();
            $table->integer('biaya_per_km');
            $table->integer('tarif');
            $table->string('jarak_maksimal', 255);
            $table->text('informasi_pembatalan');
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
        Schema::dropIfExists('pengaturan_telemedicine');
    }
}
