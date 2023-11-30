<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJarakKeLokasiToPermintaanHcTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('permintaan_hc', function (Blueprint $table) {
            $table->string('jarak_ke_lokasi');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('permintaan_hc', function (Blueprint $table) {
            $table->dropColumn('jarak_ke_lokasi');
        });
    }
}
