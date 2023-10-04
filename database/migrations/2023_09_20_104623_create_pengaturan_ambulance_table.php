<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePengaturanAmbulanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pengaturan_ambulance', function (Blueprint $table) {
            $table->bigIncrements('id_pengaturan_ambulance');
            $table->string('jarak_maksimal');
            $table->string('biaya_per_km');
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
        Schema::dropIfExists('pengaturan_ambulance');
    }
}
