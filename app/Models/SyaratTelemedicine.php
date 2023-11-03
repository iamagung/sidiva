<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyaratTelemedicine extends Model
{
    use HasFactory;
    protected $table = 'syarat_aturan_telemedicine';
    protected $primaryKey = 'id_syarat_aturan_telemedicine';
}
