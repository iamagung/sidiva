<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddToLayananPermintaanHcTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('layanan_permintaan_hc', function (Blueprint $table) {
            $table->string('alasan_penambahan')->nullable();
            $table->string('file')->nullable();
            $table->boolean('is_confirm_pasien')->nullable();
            $table->boolean('is_tambahan')->nullable();
            $table->boolean('status_bayar')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('layanan_permintaan_hc', function (Blueprint $table) {
            $table->dropColumn('alasan_penambahan');
            $table->dropColumn('file');
            $table->dropColumn('is_confirm_pasien');
            $table->dropColumn('is_tambahan');
            $table->dropColumn('status_bayar');
        });
    }
}
