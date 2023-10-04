<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LayananTelemedicine extends Model
{
    use HasFactory;
    protected $table = 'layanan_telemedicine';
    protected $primaryKey = 'id_layanan_telemedicine';

    public function permintaan_telemedicine(){
        return $this->hasMany('App\Models\Permintaantelemedicine', 'id_layanan_telemedicine');
    }
}
