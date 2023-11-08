<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Config;

class PaymentPermintaan extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_payment_permintaan';
    public function __construct(){
        $this->setConnection('mysql');
		$this->table = Config::get('database.connections.mysql.database').'.payment_permintaan';
    }

    public function permintaan_telemedicine() {
        return $this->belongsTo(PermintaanTelemedicine::class, 'permintaan_id', 'id_permintaan_telemedicine')->where('payment_permintaan.jenis_layanan', 'telemedicine');
    }
}
