<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Config;
use App\Models\DBRSUD\TmPoli;
// use Hoyvoy\CrossDatabase\Eloquent\Model;

class TenagaMedisTelemedicine extends Model{
	use HasFactory;
	// protected $connection = 'mysql';
    // protected $connection = 'mysql';
    // protected $table_name = 'mysql.tenaga_medis_telemedicine';
    // protected $table = 'tenaga_medis_telemedicine';
	protected $primaryKey = 'id_tenaga_medis';
    // protected $table = 'tenaga_medis_telemedicine';
	public function __construct(){
        $this->setConnection('mysql');
        // $this->connection('mysql');
        // $this->connection = Config::get('database.default');
        // $this->connection = 'mysql';
		$this->table = Config::get('database.connections.mysql.database').'.tenaga_medis_telemedicine';
		// $this->table = 'sidiva.ftenaga_medis_telemedicine';
        // parent::__construct($attributes);
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
	// public function tm_poli(){
		// return $this->belongsTo('App\Models\DBRSUD\TmPoli', 'poli_id', 'KodePoli');
		// return $this->belongsTo(TmPoli::class, 'poli_id', 'KodePoli');
		return $this->setConnection('dbrsud')->belongsTo('App\Models\DBRSUD\TmPoli', 'poli_id', 'KodePoli');
	}

	public function user(){
		return $this->hasOne('App\Models\User', 'id', 'nakes_id');
	}

	public function getNamaNakesAttribute($value){
		return ucfirst($value);
	}
}
