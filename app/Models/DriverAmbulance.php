<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverAmbulance extends Model
{
    use HasFactory;
    protected $table = 'driver_ambulance';
    protected $primaryKey = 'id_driver';
}
