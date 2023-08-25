<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiMCU extends Model
{
    use HasFactory;
    protected $table = 'transaksi_mcu';
    protected $primaryKey = 'id_transaksi_mcu';
}
