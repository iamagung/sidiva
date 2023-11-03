<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Config;

class TenagaMedisTelemedicine extends Model{
	use HasFactory;
	protected $connection = 'mysql';
	protected $primaryKey = 'id_tenaga_medis';
	public function __construct(){
		$this->table = Config::get('database.connections.mysql.database').'.tenaga_medis_telemedicine';
	}
	
	/**
	* Get the userRanap associated with the TenagaMedisTelemedicine
	*
	* @return \Illuminate\Database\Eloquent\Relations\HasOne
	*/
	public function user_ranap(){
		return $this->belongsTo('App\Models\DBRANAP\Users', 'nakes_id', 'id');
	}
	
	public function jadwalMedis(){
		return $this->hasMany('App\Models\JadwalTenagaMedis', 'nakes_id', 'nakes_id');
	}
	
	public function permintaan(){
		return $this->hasMany('App\Models\PermintaanTelemedicine', 'tenaga_medis_id', 'nakes_id');
	}

	public function permintaan_perawat(){
		return $this->hasMany('App\Models\PermintaanTelemedicine', 'perawat_id', 'nakes_id');
	}
	
	public function tmPoli(){
		return $this->belongsTo('App\Models\DBRSUD\TmPoli', 'poli_id', 'KodePoli');
	}
	
	// public function ratingMedis(){
	// 	return $this->hasMany('App\Models\JadwalTenagaMedis', 'nakes_id', 'nakes_id');
	// }
	
	public function getNamaNakesAttribute($value){
		return ucfirst($value);
	}
}
