<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToTenagaMedisTelemedicineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tenaga_medis_telemedicine', function (Blueprint $table) {
            $table->integer('tarif')->nullable();
            $table->string('status', 50)->default('tidak melayani');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tenaga_medis_telemedicine', function (Blueprint $table) {
            //
        });
    }
}
