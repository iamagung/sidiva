<?php

namespace App\Models\DBRANAP;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Config;

class Users extends Model{
	use HasFactory;
	// protected $connection = 'dbranap';
	// protected $table = 'users';
	// protected $primaryKey = 'id';
	// public $incrementing = false;
	public function __construct(){
        $this->setConnection('dbranap');
		$this->table = Config::get('database.connections.dbranap.database').'.users';
	}

	public function tenaga_medis_telemedicine(){
		// return $this->belongsTo('App\Models\TenagaMedisTelemedicine', 'nakes_id', 'id');
		return $this->hasOne('App\Models\TenagaMedisTelemedicine', 'nakes_id', 'id');
	}
	
	public function permintaan(){
		return $this->hasMany('App\Models\PermintaanTelemedicine', 'tenaga_medis_id', 'id');
	}

	public function permintaan_perawat(){
		return $this->hasMany('App\Models\PermintaanTelemedicine', 'perawat_id', 'id');
	}
}
