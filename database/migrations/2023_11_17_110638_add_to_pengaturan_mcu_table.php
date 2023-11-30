<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddToPengaturanMcuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pengaturan_mcu', function (Blueprint $table) {
            $table->integer('jarak_maksimal');
            $table->decimal('biaya_per_km');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pengaturan_mcu', function (Blueprint $table) {
            $table->dropColumn('jarak_maksimal');
            $table->dropColumn('biaya_per_km');
        });
    }
}
