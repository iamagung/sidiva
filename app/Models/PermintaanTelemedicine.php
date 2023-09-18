<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanTelemedicine extends Model
{
    use HasFactory;
    protected $table = 'permintaan_telemedicine';
    protected $primaryKey = 'id_permintaan_telemedicine';
}
