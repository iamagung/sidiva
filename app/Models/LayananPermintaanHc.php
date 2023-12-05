<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LayananPermintaanHc extends Model
{
    use HasFactory;
    protected $table = 'layanan_permintaan_hc';
    protected $primaryKey = 'id_layanan_permintaan_hc';

    public function layanan_hc() {
        return $this->hasOne(LayananHC::class,'id_layanan_hc','layanan_id');
    }

    public function permintaan_hc() {
        return $this->belongsTo(PermintaanHC::class,'permintaan_id','id_permintaan_hc');
    }
}
