<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthenticationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('authentication', function (Blueprint $table) {
            $table->id();
            $table->string('wa', 20);
            $table->string('otp')->comment('0=belum digunakan, 1=sudah digunakan');
            $table->string('expired', 5);
            $table->dateTime('tanggal_waktu');
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
        Schema::dropIfExists('authentication');
    }
}
