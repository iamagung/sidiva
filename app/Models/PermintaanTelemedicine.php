<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Config;

class PermintaanTelemedicine extends Model
{
  use HasFactory;
  protected $primaryKey = 'id_permintaan_telemedicine';
  public function __construct(){
        $this->setConnection('mysql');
		$this->table = Config::get('database.connections.mysql.database').'.permintaan_telemedicine';
    }

    public function user_android() {
        return $this->belongsTo(UsersAndroid::class, 'nik', 'nik');
    }

  public function resep_obat()
  {
    return $this->hasOne('App\Models\ResepObat', 'permintaan_id', 'id_permintaan_telemedicine');
  }

  public function dokter()
  {
    return $this->belongsTo('App\Models\TenagaMedisTelemedicine', 'tenaga_medis_id', 'nakes_id');
  }

  public function perawat()
  {
    return $this->belongsTo('App\Models\TenagaMedisTelemedicine', 'perawat_id', 'nakes_id');
  }

  public function rating() {
    return $this->hasOne('App\Models\RatingTelemedicine', 'permintaan_telemedicine_id', 'id_permintaan_telemedicine');
  }

  public function tmPoli()
  {
    return $this->belongsTo('App\Models\DBRSUD\TmPoli', 'poli_id', 'KodePoli');
  }

  // untuk profil nakes spt: [nama,is_active]
  public function nakes() {
    return $this->belongsTo('App\Models\DBRANAP\Users', 'tenaga_medis_id', 'id');
  }

  public function rekap_medik() {
    return $this->hasOne(RekapMedik::class, 'permintaan_id', 'id_permintaan_telemedicine')->where('rekap_medik.jenis_layanan', 'telemedicine');
  }

  public function rekam_medis_lanjutan() {
    return $this->hasOne(RekamMedisLanjutan::class, 'permintaan_id', 'id_permintaan_telemedicine')->where('rekam_medis_lanjutan.jenis_layanan', 'telemedicine');
  }

  // public function layanan_telemedicine(){
  //   return $this->belongsTo('App\Models\LayananTelemedicine','layanan_telemedicine_id','id_layanan_telemedicine');
  // }

}
