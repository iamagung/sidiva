<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaturanMcu extends Model
{
    use HasFactory;
    protected $table = 'pengaturan_mcu';
    protected $primaryKey = 'id_pengaturan_mcu';
}
