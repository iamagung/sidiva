<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaturanAmbulance extends Model
{
    use HasFactory;
    protected $table = 'pengaturan_ambulance';
    protected $primaryKey = 'id_pengaturan_ambulance';
}
