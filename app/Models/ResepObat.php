<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Config;

class ResepObat extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_resep_obat';
    // public $incrementing = false;
    public function __construct(){
        $this->setConnection('mysql');
        $this->table = Config::get('database.connections.mysql.database').'.resep_obat';
    }

    public function permintaan_telemedicine()
    {
        return $this->belongsTo(PermintaanTelemedicine::class, 'permintaan_id', 'id_permintaan_telemedicine')->where('jenis_layanan', 'telemedicine');
    }

    public function permintaan_hc()
    {
        return $this->belongsTo(PermintaanTelemedicine::class, 'permintaan_id', 'id_permintaan_telemedicine')->where('jenis_layanan', 'homecare');
    }

    public function resep_obat_detail()
    {
        return $this->hasMany(ResepObatDetail::class, 'resep_obat_id', 'id_resep_obat');
    }
}
