<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanAmbulance extends Model
{
    use HasFactory;
    protected $table = 'permintaan_ambulance';
    protected $primaryKey = 'id_permintaan_ambulance';
}
