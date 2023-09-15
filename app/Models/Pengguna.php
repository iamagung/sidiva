<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengguna extends Model
{
    use HasFactory;
    // protected $connection = 'dbapm';
    protected $table = 'users';
    protected $primaryKey = "id";
}
