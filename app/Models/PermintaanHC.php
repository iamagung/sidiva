<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanHC extends Model
{
    use HasFactory;
    protected $table = 'permintaan_hc';
    protected $primaryKey = 'id_permintaan_hc';

    public function user_android() {
        return $this->belongsTo(UsersAndroid::class, 'nik', 'nik');
    }

    public function layanan_hc(){
		return $this->belongsTo('App\Models\LayananHC','layanan_hc_id','id_layanan_hc');
	}

    public function layanan_permintaan_hc() {
        return $this->hasMany(LayananPermintaanHc::class,'permintaan_id','id_permintaan_hc');
    }

    public function rating() {
        return $this->hasOne('App\Models\Rating', 'permintaan_id', 'id_permintaan_hc');
    }

    public function rekam_medis_lanjutan() {
        return $this->hasOne(RekamMedisLanjutan::class, 'permintaan_id', 'id_permintaan_hc')->where('rekam_medis_lanjutan.jenis_layanan', 'homecare');
    }

    public function resep_obat()
    {
        return $this->hasOne('App\Models\ResepObat', 'permintaan_id', 'id_permintaan_hc');
    }

    public function payment_permintaan_eresep() {
		return $this->hasOne(PaymentPermintaan::class, 'permintaan_id', 'id_permintaan_hc')->where('payment_permintaan.jenis_layanan', 'eresep_homecare');
	}

    public function payment_permintaan() {
		return $this->hasOne(PaymentPermintaan::class, 'permintaan_id', 'id_permintaan_hc')->where('payment_permintaan.jenis_layanan', 'homecare');
	}
}
