<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Config;

class VideoConference extends Model
{
    use HasFactory;
	protected $connection = 'mysql';
	protected $primaryKey = 'id_video_conference';
	public function __construct(){
		$this->table = Config::get('database.connections.mysql.database').'.video_conference';
	}

    public function permintaan_telemedicine()
    {
        return $this->belongsTo(PermintaanTelemedicine::class, 'permintaan_id', 'id_permintaan_telemedicine');
    }
}
