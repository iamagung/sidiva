<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenagaMedis extends Model
{
    use HasFactory;
    protected $table = 'tenaga_medis';
    protected $primaryKey = 'id_tenaga_medis';
}
