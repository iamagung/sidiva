<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LayananMcu extends Model
{
    use HasFactory;
    protected $table = 'layanan_mcu';
    protected $primaryKey = 'id_layanan';
}
