<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersAndroid extends Model
{
    use HasFactory;
    protected $table = 'users_android';
    protected $primaryKey = 'id_users_android';
    protected $connection = 'mysql';

    public function permintaan_telemedicine()
    {
        return $this->hasMany(PermintaanTelemedicine::class, 'nik', 'nik');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
