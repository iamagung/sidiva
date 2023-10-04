<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyaratAmbulance extends Model
{
    use HasFactory;
    protected $table = 'syarat_aturan_ambulance';
    protected $primryKey = 'id_syarat_aturan_ambulance';
}
