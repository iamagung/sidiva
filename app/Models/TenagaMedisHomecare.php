<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenagaMedisHomecare extends Model
{
    use HasFactory;
    protected $table = 'tenaga_medis_homecare';
    protected $primaryKey = 'id_tenaga_medis';
}
