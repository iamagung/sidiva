<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LayananPermintaanMcu extends Model
{
    use HasFactory;
    protected $table = 'layanan_permintaan_mcu';
    protected $primaryKey = 'id_layanan_permintaan_mcu';

    public function layanan_mcu() {
        return $this->hasOne(LayananMcu::class,'id_layanan','layanan_id');
    }

    public function permintaan_mcu() {
        return $this->belongsTo(PermintaanMcu::class,'permintaan_id','id_permintaan');
    }
}
