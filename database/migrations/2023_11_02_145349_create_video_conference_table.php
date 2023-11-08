<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideoConferenceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video_conference', function (Blueprint $table) {
            $table->bigIncrements('id_video_conference');
            $table->integer('permintaan_id');
            $table->string('jenis_layanan', 25);
            $table->string('link_vicon', 255);
            $table->boolean('is_expired')->default(false);
            $table->dateTime('tgl_expired')->nullable();
            $table->dateTime('waktu_mulai')->nullable();
            $table->dateTime('waktu_selesai')->nullable();
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
        Schema::dropIfExists('video_conference');
    }
}
