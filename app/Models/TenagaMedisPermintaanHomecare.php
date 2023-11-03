<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenagaMedisPermintaanHomecare extends Model
{
    use HasFactory;
    protected $table = 'tenaga_medis_permintaan_hc';
    protected $primaryKey = 'id_tenaga_medis_permintaan_hc';
}
