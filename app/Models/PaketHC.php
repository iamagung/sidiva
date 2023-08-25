<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaketHC extends Model
{
    use HasFactory;
    protected $table = 'paket_hc';
    protected $primaryKey = 'id_paket_hc';
}
