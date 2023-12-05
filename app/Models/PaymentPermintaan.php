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
        return $this->belongsTo(PermintaanTelemedicine::class, 'permintaan_id', 'id_permintaan_telemedicine');
    }

    public function resep_obat_telemedicine() {
        return $this->belongsTo(PermintaanTelemedicine::class, 'permintaan_id', 'id_permintaan_telemedicine');
    }

    public function resep_obat_hc() {
        return $this->belongsTo(PermintaanHC::class, 'permintaan_id', 'id_permintaan_hc');
    }

    public function get_telemedicine() {
        return $this->where('jenis_layanan', 'telemedicine');
    }

    public function permintaan_hc() {
        return $this->belongsTo(PermintaanHC::class, 'permintaan_id', 'id_permintaan_hc');
    }

    public function permintaan_mcu() {
        return $this->belongsTo(PermintaanMcu::class, 'permintaan_id', 'id_permintaan');
    }
}
