<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LayananMcu extends Model
{
    use HasFactory;
    protected $table = 'layanan_mcu';
    protected $primaryKey = 'id_layanan';

    public function layanan_permintaan_mcu() {
        return $this->belongsTo(LayananPermintaanMcu::class,'id_layanan','layanan_id');
    }
}
