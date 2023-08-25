<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaturanHC extends Model
{
    use HasFactory;
    protected $table = 'pengaturan_hc';
    protected $primaryKey = 'id_pengaturan_hc';
}
