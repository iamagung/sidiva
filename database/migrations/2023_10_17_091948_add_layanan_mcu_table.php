<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLayananMcuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('layanan_mcu', function(Blueprint $table) {
            $table->string('jenis_layanan');
            $table->string('maksimal_peserta')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('layanan_mcu', function(Blueprint $table) {
            $table->dropColumn('jenis_layanan');
            $table->dropColumn('maksimal_peserta');
        });
    }
}
