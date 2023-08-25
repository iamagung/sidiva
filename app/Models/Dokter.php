<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokter extends Model
{
    use HasFactory;
    protected $connection = 'dbrsud';
    protected $table = 'dokter_bridg';
    protected $primaryKey = "id";
}
