<?php

namespace App\Models\DBRSUD;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Hoyvoy\CrossDatabase\Eloquent\Model;
use App\Models\TenagaMedisTelemedicine;
use Config;

class TmPoli extends Model
{
    use HasFactory;
    // protected $connection = 'dbrsud';
    protected $connection = 'dbrsud';
    // protected $table_name = 'dbrsud.tm_poli';
    protected $table = 'tm_poli';
    protected $primaryKey = 'KodePoli';
    // protected $table = 'tm_poli';
    public $incrementing = false;

	public function __construct(){
        // $this->setConnection('dbrsud');
        // $this->connection('dbrsud');
        // in belongsTo relationship, default connection not used
        // $this->connection = Config::get('database.default');
        // $this->connection = 'dbrsud';
		// $this->table = Config::get('database.connections.dbrsud.database').'.tm_poli';
		// $this->table = 'db_simars.tm_poli';
        // parent::__construct($attributes);
	}

    public function tenagaMedis()
    // public function tenaga_medis_telemedicine()
    {
        // return $this->setConnection('mysql')->hasMany(TenagaMedisTelemedicine::class, 'poli_id', 'KodePoli');
        // return $this->hasMany(TenagaMedisTelemedicine::class, 'poli_id', 'KodePoli');
        // return $this->setConnection('mysql')->hasMany('App\Models\TenagaMedisTelemedicine', 'poli_id', 'KodePoli');
        $con = $this->setConnection('mysql')->hasMany(TenagaMedisTelemedicine::class, 'poli_id', 'KodePoli');
        $con->table = 'sidiva.tenaga_medis_telemedicine';
        return $con;
    }

    public function permintaan()
    {
        return $this->hasMany('App\Models\PermintaanTelemedicine', 'poli_id', 'KodePoli');
    }
}
