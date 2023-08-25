<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanHC extends Model
{
    use HasFactory;
    protected $table = 'permintaan_hc';
    protected $primaryKey = 'id_permintaan_hc';

    public function layanan_hc(){
		return $this->belongsTo('App\Models\LayananHC','layanan_hc_id','id_layanan_hc');
	}

}
