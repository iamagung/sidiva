<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanMcu extends Model
{
    use HasFactory;
    protected $table = 'permintaan_mcu';
    protected $primaryKey = 'id_permintaan';
}
