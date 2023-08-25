<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyaratMcu extends Model
{
    use HasFactory;
    protected $table = 'syarat_mcu';
    protected $primryKey = 'id_syarat_mcu';
}
