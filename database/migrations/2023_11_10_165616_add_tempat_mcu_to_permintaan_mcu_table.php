<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTempatMcuToPermintaanMcuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('permintaan_mcu', function (Blueprint $table) {
            $table->string('tempat_mcu')->comment('RS, Luar RS')->nullable();
            $table->string('jarak_ke_lokasi')->nullable();
            $table->date('date_mcu')->nullable();
            $table->time('time_mcu')->nullable();
            $table->decimal('biaya_ke_lokasi',12,2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('permintaan_mcu', function (Blueprint $table) {
            $table->dropColumn('tempat_mcu');
            $table->dropColumn('jarak_ke_lokasi');
            $table->dropColumn('date_mcu');
            $table->dropColumn('time_mcu');
            $table->dropColumn('biaya_ke_lokasi');
        });
    }
}
