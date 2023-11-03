<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Config;

class RekamMedisLanjutan extends Model
{
    use HasFactory;
	protected $connection = 'mysql';
	protected $primaryKey = 'id_rekam_medis_lanjutan';
	public function __construct(){
		$this->table = Config::get('database.connections.mysql.database').'.rekam_medis_lanjutan';
	}

    public function permintaan_telemedicine()
    {
        return $this->belongsTo(PermintaanTelemedicine::class, 'permintaan_id', 'id_permintaan_telemedicine');
    }

    public function perawat()
    {
        return $this->hasOne(TenagaMedisTelemedicine::class, 'nakes_id', 'perawat_id');
    }
}
