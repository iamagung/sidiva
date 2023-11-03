<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Config;

class RekapMedik extends Model
{
    use HasFactory;
	protected $connection = 'mysql';
	protected $primaryKey = 'id_rekap_medik';
	public function __construct(){
		$this->table = Config::get('database.connections.mysql.database').'.rekap_medik';
	}

    public function permintaan_telemedicine()
    {
        return $this->belongsTo(PermintaanTelemedicine::class, 'permintaan_id', 'id_permintaan_telemedicine');
    }

    public function dokter()
    {
        return $this->hasOne(TenagaMedisTelemedicine::class, 'nakes_id', 'dokter_id');
    }
}
