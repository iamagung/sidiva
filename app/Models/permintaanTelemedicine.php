<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanTelemedicine extends Model
{
    use HasFactory;
    protected $table = 'permintaan_telemedicine';
    protected $primaryKey = 'id_permintaan_telemedicine';

    public function layanan_telemedicine(){
		return $this->belongsTo('App\Models\LayananTelemedicine','layanan_telemedicine_id','id_layanan_telemedicine');
	}

}
