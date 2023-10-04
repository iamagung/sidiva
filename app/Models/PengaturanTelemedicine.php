<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaturanTelemedicine extends Model
{
    use HasFactory;
    protected $table = 'pengaturan_telemedicine';
    protected $primaryKey = 'id_pengaturan_telemedicine';
}
