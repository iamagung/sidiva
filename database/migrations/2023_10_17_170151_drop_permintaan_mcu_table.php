<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropPermintaanMcuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('permintaan_mcu', function (Blueprint $table) {
            $table->dropColumn('kode_booking');
            $table->dropColumn('no_antrian');
            $table->dropColumn('no_bpjs');
            $table->dropColumn('no_rujukan');
            $table->dropColumn('jenis_pembayaran');
            $table->dropColumn('layanan_id');
            $table->dropColumn('biaya');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
