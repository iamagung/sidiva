<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LayananHC extends Model
{
    use HasFactory;
    protected $table = 'layanan_hc';
    protected $primaryKey = 'id_layanan_hc';

    public function permintaan_hc(){
        return $this->hasMany('App\Models\PermintaanHC', 'id_layanan_hc');
    }

    public function layanan_permintaan_hc() {
        $this->belongsTo(LayananPermintaanHc::class,'id_layanan_hc','layanan_id');
    }
}
