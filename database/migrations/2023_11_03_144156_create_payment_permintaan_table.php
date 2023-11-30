<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentPermintaanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_permintaan', function (Blueprint $table) {
            $table->bigIncrements('id_payment_permintaan');
            $table->integer('permintaan_id');
            $table->string('invoice_id', 255)->nullable();
            $table->text('nomor_referensi')->nullable();
            $table->decimal('nominal', 12, 2);
            $table->decimal('ongkos_kirim', 12, 2)->default(0);
            $table->dateTime('tgl_lunas')->nullable();
            $table->dateTime('tgl_expired');
            $table->string('jenis_layanan', 25);
            $table->string('status', 25);
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
        Schema::dropIfExists('payment_permintaan');
    }
}
