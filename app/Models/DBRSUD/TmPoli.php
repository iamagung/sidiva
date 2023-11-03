<?php

namespace App\Models\DBRSUD;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TmPoli extends Model
{
    use HasFactory;
    protected $connection = 'dbrsud';
    protected $table = 'tm_poli';
    protected $primaryKey = 'KodePoli';
    public $incrementing = false;

    public function tenagaMedis()
    {
        return $this->hasMany('App\Models\TenagaMedisTelemedicine', 'poli_id', 'KodePoli');
    }

    public function permintaan()
    {
        return $this->hasMany('App\Models\PermintaanTelemedicine', 'poli_id', 'KodePoli');
    }
}
