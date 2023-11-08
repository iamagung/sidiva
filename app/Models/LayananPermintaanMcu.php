<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LayananPermintaanMcu extends Model
{
    use HasFactory;
    protected $table = 'layanan_permintaan_mcu';
    protected $primaryKey = 'id_layanan_permintaan_mcu';
}
