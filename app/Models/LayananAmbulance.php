<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LayananAmbulance extends Model
{
    use HasFactory;
    protected $table = 'layanan_ambulance';
    protected $primaryKey = 'id_layanan_ambulance';
}
